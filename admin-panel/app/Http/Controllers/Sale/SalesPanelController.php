<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SalesPerson;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\SaleReturn;
use App\Models\Attendance;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalesPanelController extends Controller
{
    private function baseData(): array
    {
        $user        = Auth::user();
        $salesPerson = SalesPerson::where('email', $user?->email)->first();
        $settings    = AdminSetting::first();
        return compact('user', 'salesPerson', 'settings');
    }

    // ─── DASHBOARD ────────────────────────────────────────────────────────────
    public function dashboard()
    {
        extract($this->baseData());
        $uid = Auth::id();

        $totalPayments  = Payment::where('created_by', $uid)->sum('amount');
        $totalSales     = Order::where('created_by', $uid)->sum('total_amount') ?: Order::where('created_by', $uid)->sum('amount');
        $todayOrders    = Order::where('created_by', $uid)->whereDate('created_at', today())->count();
        $totalCustomers = Customer::count();
        $recentOrders   = Order::with('store')->where('created_by', $uid)->latest()->take(5)->get();

        return view('sale.panel.dashboard', compact(
            'user','salesPerson','settings',
            'totalPayments','totalSales','todayOrders',
            'totalCustomers','recentOrders'
        ));
    }

    // ─── PARTIES (CUSTOMERS) ──────────────────────────────────────────────────
    public function parties(Request $request)
    {
        extract($this->baseData());
        $search = $request->get('search', '');
        $filter = $request->get('filter', 'all');
        $query  = Customer::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%");
            });
        }
        if ($filter === 'visited')  $query->whereHas('payments');
        if ($filter === 'pending')  $query->where(fn($q) => $q->whereNull('credit_limit')->orWhere('credit_limit', '>', 0));

        $parties = $query->latest()->paginate(20);
        return view('sale.panel.parties.index', compact('user','salesPerson','settings','parties','search','filter'));
    }

    public function partyShow($id)
    {
        extract($this->baseData());
        $party    = Customer::findOrFail($id);
        $orders   = Order::where(fn($q) => $q->where('customer_name', $party->business_name)
                                             ->orWhere('customer_phone', $party->mobile))
                         ->latest()->take(10)->get();
        $payments = Payment::where('customer_id', $id)->latest()->take(10)->get();

        // Due = total unpaid order amount - payments received
        // opening_balance is NOT included as due (it's a ledger balance, not an order debt)
        $totalOrders   = $orders->sum(fn($o) => (float)($o->total_amount ?? $o->amount ?? 0));
        $totalPaid     = $payments->sum('amount');
        $totalDue      = max(0, $totalOrders - $totalPaid);

        return view('sale.panel.parties.show', compact('user','salesPerson','settings','party','orders','payments','totalDue'));
    }

    public function partyCreate()
    {
        extract($this->baseData());
        return view('sale.panel.parties.create', compact('user','salesPerson','settings'));
    }

    public function partyStore(Request $request)
    {
        $data = $request->validate([
            'business_name'   => 'required|string|max:255',
            'mobile'          => 'required|string|max:20',
            'email'           => 'nullable|email|max:255',
            'gstin'           => 'nullable|string|max:20',
            'contact_person'  => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'route'           => 'nullable|string|max:100',
            'credit_limit'    => 'nullable|numeric',
            'credit_period'   => 'nullable|integer',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
        ]);
        if (!empty($data['latitude']) && !empty($data['longitude'])) {
            $data['geolocation'] = $data['latitude'] . ',' . $data['longitude'];
        }
        unset($data['latitude'], $data['longitude']);
        Customer::create($data);
        return redirect()->route('sale.panel.parties')->with('success', 'Party added successfully!');
    }

    public function partyEdit($id)
    {
        extract($this->baseData());
        $party = Customer::findOrFail($id);
        return view('sale.panel.parties.edit', compact('user','salesPerson','settings','party'));
    }

    public function partyUpdate(Request $request, $id)
    {
        $party = Customer::findOrFail($id);
        $data  = $request->validate([
            'business_name'   => 'required|string|max:255',
            'mobile'          => 'required|string|max:20',
            'email'           => 'nullable|email',
            'gstin'           => 'nullable|string|max:20',
            'contact_person'  => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'route'           => 'nullable|string|max:100',
            'credit_limit'    => 'nullable|numeric',
            'credit_period'   => 'nullable|integer',
        ]);
        $party->update($data);
        return redirect()->route('sale.panel.party.show', $id)->with('success', 'Party updated!');
    }

    // ─── ITEMS (PRODUCTS) — only Active products from admin ───────────────────
    public function items(Request $request)
    {
        extract($this->baseData());
        $search   = $request->get('search', '');
        $category = $request->get('category', 'all');

        $query = Product::where('status', 'Active');
        if ($search)             $query->where('name', 'like', "%$search%");
        if ($category !== 'all') $query->where('category', $category);

        $products   = $query->orderBy('name')->paginate(24);
        $categories = Product::where('status', 'Active')
                              ->select('category')->distinct()
                              ->whereNotNull('category')->pluck('category');

        return view('sale.panel.items.index', compact('user','salesPerson','settings','products','categories','search','category'));
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'sale_price'         => 'required|numeric|min:0',
            'mrp'                => 'required|numeric|min:0',
            'unit'               => 'required|string|max:50',
            'category'           => 'required|string|max:255',
            'stock'              => 'nullable|integer|min:0',
            'hsn_code'           => 'nullable|string|max:50',
            'gst_percent'        => 'nullable|numeric|min:0|max:100',
            'price_includes_gst' => 'nullable|boolean',
            'images'             => 'nullable|array|max:2',
            'images.*'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $stock     = (int)($data['stock'] ?? 0);
        $salePrice = (float)$data['sale_price'];
        $mrp       = (float)$data['mrp'];
        $gstPct    = (float)($data['gst_percent'] ?? 0);
        $inclGst   = $request->boolean('price_includes_gst');

        if ($inclGst && $gstPct > 0) {
            $base      = $salePrice / (1 + ($gstPct / 100));
            $gstAmount = $salePrice - $base;
            $final     = $salePrice;
        } else {
            $base      = $salePrice;
            $gstAmount = ($gstPct > 0) ? ($salePrice * ($gstPct / 100)) : 0;
            $final     = $salePrice + $gstAmount;
        }

        $purchasePrice = 0.0;
        $margin        = $base - $purchasePrice;

        do {
            $sku = 'SP' . now()->format('ymdHis') . Str::upper(Str::random(4));
        } while (Product::where('sku', $sku)->exists());

        $imagePath = null;
        $files = $request->file('images', []);
        if (!empty($files[0])) {
            $imagePath = $files[0]->store('products', 'public');
        }

        $product = Product::create([
            'name'               => $data['name'],
            'sku'                => $sku,
            'unit'               => $data['unit'],
            'item_code'          => $sku,
            'brand'              => 'General',
            'category'           => $data['category'],
            'purchase_price'     => $purchasePrice,
            'sell_price'         => $salePrice,
            'sale_price'         => $salePrice,
            'mrp'                => $mrp,
            'margin'             => round($margin, 2),
            'status'             => 'Active',
            'image'              => $imagePath,
            'quantity'           => $stock,
            'available_units'    => $stock,
            'gst_percent'        => $gstPct,
            'gst_amount'         => round($gstAmount, 2),
            'price_includes_gst' => $inclGst,
            'final_price'        => round($final, 2),
            'hsn_code'           => $data['hsn_code'] ?? null,
        ]);

        foreach (array_slice($files, 1) as $img) {
            if (!$img) continue;
            $path = $img->store('products/gallery', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }

        return redirect()->route('sale.panel.items')->with('success', 'Item added!');
    }

    // ─── NEW SALE FLOW ────────────────────────────────────────────────────────
    public function newSale(Request $request)
    {
        extract($this->baseData());
        $partyId    = $request->get('party_id');
        $party      = $partyId ? Customer::find($partyId) : null;
        $parties    = Customer::orderBy('business_name')->get();
        $products   = Product::where('status', 'Active')->orderBy('name')->get();
        $categories = Product::where('status', 'Active')
                              ->select('category')->distinct()
                              ->whereNotNull('category')->pluck('category');

        return view('sale.panel.sale.create', compact('user','salesPerson','settings','party','parties','products','categories'));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $customer    = Customer::findOrFail($request->customer_id);
            $uid         = Auth::id();
            $sp          = SalesPerson::where('email', Auth::user()?->email)->first();
            $lineItems   = [];
            $subtotal    = 0;

            foreach ($request->items as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $qty       = (int) $item['quantity'];
                $price     = (float) ($product->sale_price ?? $product->mrp ?? 0);
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'quantity'     => $qty,
                    'unit_price'   => $price,
                    'subtotal'     => $lineTotal,
                    'total'        => $lineTotal,
                ];
            }

            $orderNumber = 'ORD' . now()->format('YmdHis') . rand(10, 99);

            // Link order to a Store record if one exists for this customer
            $linkedStore = \App\Models\Store::where('phone', $customer->mobile)
                ->orWhere('store_name', $customer->business_name)
                ->first();

            $order = Order::create([
                'order_number'    => $orderNumber,
                'customer_name'   => $customer->business_name,
                'customer_phone'  => $customer->mobile,
                'store_id'        => $linkedStore?->id,
                'amount'          => $subtotal,
                'total_amount'    => $subtotal,
                'status'          => 'Pending',
                'created_by'      => $uid,
                'sales_person_id' => $sp?->id,
                'notes'           => $request->notes,
                'order_date'      => now()->toDateString(),
            ]);

            foreach ($lineItems as $line) {
                OrderItem::create(array_merge($line, ['order_id' => $order->id]));
            }

            DB::commit();
            return redirect()->route('sale.panel.transactions')
                             ->with('success', "Sale #{$order->order_number} saved! Total: ₹" . number_format($subtotal, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    // ─── PAYMENT IN ───────────────────────────────────────────────────────────
    public function paymentIn(Request $request)
    {
        extract($this->baseData());
        $partyId         = $request->get('party_id');
        $party           = $partyId ? Customer::find($partyId) : null;
        $parties         = Customer::orderBy('business_name')->get();
        $unsettledOrders = $partyId
            ? Order::where('customer_phone', $party?->mobile)
                   ->where('status', '!=', 'Cancelled')->latest()->get()
            : collect();

        return view('sale.panel.payment.create', compact('user','salesPerson','settings','party','parties','unsettledOrders'));
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'amount'       => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:Cash,Cheque,Online,Coupon',
            'notes'        => 'nullable|string',
            'order_id'     => 'nullable|exists:orders,id',
        ]);
        $data['created_by']   = Auth::id();
        $data['payment_date'] = now()->toDateString();
        Payment::create($data);

        // Mark the linked order as Complete when fully paid
        $statusMessage = 'Payment recorded!';

        if (!empty($data['order_id'])) {
            $order = Order::find($data['order_id']);
            if ($order && $order->status === 'Pending') {
                // Sum all payments for this order
                $totalPaid = Payment::where('order_id', $order->id)->sum('amount');
                $orderAmount = (float) ($order->total_amount ?? $order->amount ?? 0);

                if ($totalPaid >= $orderAmount) {
                    $order->update(['status' => 'Complete']);
                    $statusMessage = 'Payment recorded & order marked as Complete!';
                } else {
                    $statusMessage = 'Payment recorded! (₹' . number_format($orderAmount - $totalPaid, 2) . ' remaining)';
                }
            }
        } else {
            // No specific order selected — find the oldest pending order for this customer
            $customer = Customer::findOrFail($data['customer_id']);
            $order = Order::where('customer_phone', $customer->mobile)
                         ->where('status', 'Pending')
                         ->oldest()
                         ->first();

            if ($order) {
                // Sum all payments for this order
                $totalPaid = Payment::where('order_id', $order->id)->sum('amount');
                // Also count payments by customer not linked to any order
                $unlinkedPaid = Payment::where('customer_id', $data['customer_id'])
                                      ->whereNull('order_id')
                                      ->sum('amount');
                $orderAmount = (float) ($order->total_amount ?? $order->amount ?? 0);

                if (($totalPaid + $unlinkedPaid) >= $orderAmount) {
                    $order->update(['status' => 'Complete']);
                    $statusMessage = 'Payment recorded & order marked as Complete!';
                }
            }
        }

        return redirect()->route('sale.panel.transactions')->with('success', $statusMessage);
    }

    // ─── TRANSACTIONS — scoped to this sales person ───────────────────────────
    public function transactions(Request $request)
    {
        extract($this->baseData());
        $type    = $request->get('type', 'all');
        $partyId = $request->get('party_id');
        $uid     = Auth::id();

        $ordersQ   = Order::with(['items', 'assignedDeliveryPerson', 'assignedDelivery', 'store', 'store.city'])->where('created_by', $uid);
        $paymentsQ = Payment::with('customer')->where('created_by', $uid);

        if ($partyId) {
            $customer  = Customer::find($partyId);
            $ordersQ   = $ordersQ->where(fn($q) => $q->where('customer_name', $customer?->business_name)
                                                      ->orWhere('customer_phone', $customer?->mobile));
            $paymentsQ = $paymentsQ->where('customer_id', $partyId);
        }

        $orders   = $ordersQ->latest()->paginate(15, ['*'], 'orders_page');
        $payments = $paymentsQ->latest()->paginate(15, ['*'], 'payments_page');
        $parties  = Customer::orderBy('business_name')->get();

        return view('sale.panel.transactions.index', compact(
            'user','salesPerson','settings','orders','payments','parties','type','partyId'
        ));
    }

    // ─── RETURNS — scoped to this sales person ────────────────────────────────
    public function returns(Request $request)
    {
        extract($this->baseData());
        $uid     = Auth::id();
        $returns = SaleReturn::with(['order','customer'])
                             ->where('created_by', $uid)->latest()->paginate(15);
        $orders  = Order::where('created_by', $uid)
                        ->where('status', '!=', 'Cancelled')->latest()->get();

        return view('sale.panel.returns.index', compact('user','salesPerson','settings','returns','orders'));
    }

    public function storeReturn(Request $request)
    {
        $data = $request->validate([
            'order_id'           => 'required|exists:orders,id',
            'reason'             => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $order       = Order::findOrFail($data['order_id']);
            $total       = 0;
            $returnItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty     = (int) $item['quantity'];
                $price   = (float) ($product->sale_price ?? $product->mrp ?? 0);
                $total  += $price * $qty;
                // Restore stock — keeps admin inventory in sync
                $product->increment('quantity', $qty);
                $inv = Inventory::where('product_id', $product->id)->first();
                if ($inv) $inv->increment('quantity', $qty);
                $returnItems[] = ['product_id' => $product->id, 'name' => $product->name, 'quantity' => $qty, 'price' => $price];
            }

            SaleReturn::create([
                'order_id'     => $order->id,
                'customer_id'  => null,
                'created_by'   => Auth::id(),
                'total_amount' => $total,
                'reason'       => $data['reason'],
                'items'        => $returnItems,
            ]);

            DB::commit();
            return redirect()->route('sale.panel.returns')->with('success', 'Return processed & stock restored!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    // ─── ATTENDANCE ───────────────────────────────────────────────────────────
    public function attendance(Request $request)
    {
        extract($this->baseData());
        $today       = now()->toDateString();
        $name        = $salesPerson?->name ?? $user?->name ?? 'Sales User';
        $todayRecord = Attendance::where('employee_name', $name)->whereDate('date', $today)->latest()->first();
        $records     = Attendance::where('employee_name', $name)->latest()->paginate(20);

        return view('sale.panel.attendance.index', compact('user','salesPerson','settings','todayRecord','records','today'));
    }

    public function markAttendance(Request $request)
    {
        $data = $request->validate([
            'action'           => 'required|in:in,out',
            'comments'         => 'nullable|string|max:500',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'attendance_image' => 'nullable|image|max:5120',
        ]);

        $user        = Auth::user();
        $salesPerson = SalesPerson::where('email', $user?->email)->first();
        $name        = $salesPerson?->name ?? $user?->name ?? 'Sales User';
        $today       = now()->toDateString();

        $attendance = Attendance::firstOrNew(['employee_name' => $name, 'date' => $today]);
        $attendance->status = 'Present';
        if (!empty($data['comments'])) $attendance->notes     = $data['comments'];
        if (!empty($data['latitude']))  $attendance->latitude  = $data['latitude'];
        if (!empty($data['longitude'])) $attendance->longitude = $data['longitude'];

        if ($request->hasFile('attendance_image') && $request->file('attendance_image')->isValid()) {
            $path = $request->file('attendance_image')->store('attendances', 'public');
            if ($data['action'] === 'in') {
                $attendance->image_in  = $path;
            } else {
                $attendance->image_out = $path;
            }
        }

        if ($data['action'] === 'in') {
            $attendance->time_in = now()->format('H:i:s');
        } else {
            if (empty($attendance->time_in)) $attendance->time_in = now()->format('H:i:s');
            $attendance->time_out = now()->format('H:i:s');
        }
        $attendance->save();

        $label = $data['action'] === 'in' ? 'Check-In' : 'Check-Out';
        $time  = now()->format('h:i A');
        return back()->with('att_success', "Attendance {$label} marked at {$time}");
    }

    // ─── EXPENSES — scoped to this user ───────────────────────────────────────
    public function expenses(Request $request)
    {
        extract($this->baseData());
        $uid            = Auth::id();
        $expenses       = Expense::where('created_by', $uid)->latest()->paginate(20);
        $categories     = ['Travel', 'Food', 'Accommodation', 'Communication', 'Stationery', 'Other'];
        $totalThisMonth = Expense::where('created_by', $uid)
                                 ->whereMonth('created_at', now()->month)->sum('amount');

        return view('sale.panel.expenses.index', compact('user','salesPerson','settings','expenses','categories','totalThisMonth'));
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'category'     => 'required|string|max:100',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string',
            'expense_date' => 'nullable|date',
        ]);
        $data['created_by']   = Auth::id();
        $data['expense_date'] = $data['expense_date'] ?? now()->toDateString();
        Expense::create($data);
        return back()->with('success', 'Expense added!');
    }

    // ─── ACHIEVEMENTS — scoped to this user ───────────────────────────────────
    public function achievements()
    {
        extract($this->baseData());
        $uid             = Auth::id();
        $totalSales      = Order::where('created_by', $uid)->sum('total_amount') ?: Order::where('created_by', $uid)->sum('amount');
        $totalOrders     = Order::where('created_by', $uid)->count();
        $deliveredOrders = Order::where('created_by', $uid)->where('status', 'Delivered')->count();
        $thisMonthSales  = Order::where('created_by', $uid)->whereMonth('created_at', now()->month)->sum('total_amount');
        $totalCustomers  = Customer::count();

        return view('sale.panel.achievements.index', compact(
            'user','salesPerson','settings',
            'totalSales','totalOrders','deliveredOrders','thisMonthSales','totalCustomers'
        ));
    }

    // ─── API: Active products for cart ────────────────────────────────────────
    public function apiProducts(Request $request)
    {
        $search = $request->get('search', '');
        $cat    = $request->get('category', '');
        $query  = Product::where('status', 'Active');
        if ($search) $query->where('name', 'like', "%$search%");
        if ($cat)    $query->where('category', $cat);
        return response()->json(
            $query->select(['id','name','sale_price','mrp','quantity','unit','category','image','brand'])->get()
        );
    }

    // ─── API: Customer orders for payment ─────────────────────────────────────
    public function apiCustomerOrders($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $orders   = Order::where('customer_phone', $customer->mobile)
                         ->where('status', '!=', 'Cancelled')
                         ->latest()->get(['id','order_number','total_amount','amount','status','created_at']);
        return response()->json($orders);
    }
}
