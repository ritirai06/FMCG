<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Helpers\OrderHelper;
use App\Services\OrderListService;
use App\Http\Requests\UpdateOrderStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderController extends Controller
{
    private OrderListService $orderService;

    public function __construct(OrderListService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders based on user role with filtering
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build filter array from request
        $filters = [
            'status' => $request->get('status', 'all'),
            'store' => $request->get('store', 'all'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        // Get filtered orders using OrderListService
        $orders = $this->orderService->getFilteredOrders($filters, 15);
        
        // Get available statuses and stores for filters
        $statuses = $this->orderService->getAvailableStatuses();
        $stores = $this->orderService->getStoresForFilter();
        $deliveryPersons = $this->orderService->getDeliveryPersons();
        $summary = $this->orderService->getOrdersSummary();

        return view('orders.index', compact('orders', 'statuses', 'stores', 'deliveryPersons', 'summary', 'filters', 'user'));
    }

    /**
     * Legacy list endpoint for API
     */
    public function list()
    {
        $data = Order::orderBy('id', 'desc')->get();
        return response()->json(['ok' => true, 'data' => $data]);
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $stores = Store::orderBy('store_name')->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('orders.create', compact('stores', 'products'));
    }

    /**
     * Store a newly created order in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Generate unique order number
            $orderNumber = OrderHelper::generateOrderNumber();

            // Calculate total amount
            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}");
                }

                $subtotal = $product->sale_price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->sale_price,
                    'price' => $product->sale_price,
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ];
            }

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'store_id' => $validated['store_id'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'amount' => $totalAmount,
                'total_amount' => $totalAmount,
                'status' => 'Pending',
                'created_by' => Auth::id(),
            ]);

            // Create order items and reduce stock
            foreach ($itemsData as $itemData) {
                OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));

                // Reduce product stock
                $product = Product::find($itemData['product_id']);
                $product->decrement('stock_quantity', $itemData['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', "Order {$orderNumber} created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['store', 'createdBy', 'assignedDelivery', 'items.product']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order (status/assignment)
     */
    public function edit(Order $order)
    {
        $statuses = OrderHelper::getOrderStatuses();
        $deliveryUsers = User::byRole('delivery')->active()->get();

        return view('orders.edit', compact('order', 'statuses', 'deliveryUsers'));
    }

    /**
     * Update the specified order
     */
    public function update(UpdateOrderStatusRequest $request, Order $order)
    {
        $user = Auth::user();

        // Validate authorization
        if (!$this->orderService->canEditOrder($order)) {
            abort(403, 'You are not authorized to edit this order');
        }

        $validated = $request->validated();

        // Check if user can transition to this status
        if (!$this->orderService->canTransitionStatus($order, $validated['status'])) {
            return back()->with('error', 'You cannot change to this status. Current status: ' . $order->status);
        }

        DB::beginTransaction();

        try {
            $updateData = [
                'status' => $validated['status'],
            ];

            if (!empty($validated['assigned_delivery'])) {
                $updateData['assigned_delivery'] = $validated['assigned_delivery'];
            }

            if (!empty($validated['notes'])) {
                $updateData['notes'] = $validated['notes'];
            }

            $order->update($updateData);

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', "Order status updated to {$validated['status']} successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an order (only admin and creator can cancel)
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $order->created_by !== $user->id) {
            abort(403);
        }

        if (in_array($order->status, ['Out for Delivery', 'Delivered'])) {
            return back()->with('error', 'Cannot cancel order in this status');
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->items as $item) {
                Product::find($item->product_id)->increment('stock_quantity', $item->quantity);
            }

            $order->update(['status' => 'Cancelled']);

            DB::commit();

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order cancelled and stock restored');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status via AJAX
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Approved,Packed,Out for Delivery,Delivered',
        ]);

        $user = Auth::user();

        // Check authorization
        if (!$this->orderService->canAccessOrder($order)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Check if user can transition to this status
        if (!$this->orderService->canTransitionStatus($order, $validated['status'])) {
            return response()->json([
                'error' => "You cannot transition from {$order->status} to {$validated['status']}"
            ], 403);
        }

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $order->status,
            'badge_class' => OrderHelper::getStatusBadgeClass($order->status),
            'badge_icon' => OrderHelper::getStatusIcon($order->status),
        ]);
    }

    /**
     * Assign delivery person to order
     */
    public function assignDelivery(Request $request, Order $order)
    {
        $user = Auth::user();

        // Only admin can assign
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'assigned_delivery' => 'required|exists:users,id',
        ]);

        $deliveryPerson = User::findOrFail($validated['assigned_delivery']);

        // Verify the user is a delivery person
        if (!$deliveryPerson->isDelivery()) {
            return response()->json(['error' => 'Selected user is not a delivery person'], 422);
        }

        $order->update([
            'assigned_delivery' => $validated['assigned_delivery'],
            'status' => $order->status === 'Pending' ? 'Approved' : $order->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order assigned to {$deliveryPerson->name}",
            'assigned_delivery' => $deliveryPerson->name,
        ]);
    }

    /**
     * Legacy summary endpoint
     */
    public function summary(Request $request)
    {
        $date = $request->get('date');
        if ($date) {
            try {
                $dt = Carbon::parse($date);
            } catch (\Exception $e) {
                $dt = Carbon::now();
            }
        } else {
            $dt = Carbon::now();
        }
        $year = $dt->year;
        $month = $dt->month;

        $base = Order::whereYear('order_date', $year)->whereMonth('order_date', $month);
        $totalOrders = $base->count();
        $grossSales = (float)$base->sum('amount');
        $delivered = Order::whereYear('order_date', $year)->whereMonth('order_date', $month)->where('status', 'Delivered')->count();
        $onTime = $totalOrders ? round(($delivered / $totalOrders) * 100) : 0;

        return response()->json(['ok' => true, 'data' => ['totalOrders' => $totalOrders, 'grossSales' => $grossSales, 'onTimeDelivery' => $onTime, 'month' => $dt->format('M Y')]]);
    }

    /**
     * Legacy edit endpoint
     */
    public function legacyEdit($id)
    {
        $o = Order::findOrFail($id);
        return response()->json(['ok' => true, 'data' => $o]);
    }

    /**
     * Legacy update endpoint
     */
    public function legacyUpdate(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'order_number' => 'required|unique:orders,order_number,' . $id,
            'customer' => 'nullable|string',
            'order_date' => 'nullable|date',
            'amount' => 'required|numeric',
            'status' => 'required|string'
        ]);
        if ($v->fails()) return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        $o = Order::findOrFail($id);
        $o->update($v->validated());
        return response()->json(['ok' => true, 'data' => $o]);
    }

    /**
     * Legacy destroy endpoint
     */
    public function destroy($id)
    {
        Order::destroy($id);
        return response()->json(['ok' => true]);
    }
}

