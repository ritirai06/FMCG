<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    private array $validStatuses = ['pending', 'confirmed', 'dispatched', 'delivered', 'cancelled'];

    // ── Status flow: what transitions are allowed per role ─────────
    private array $deliveryAllowed = ['confirmed', 'dispatched', 'delivered'];

    // ── Web Views ──────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user    = Auth::user();
        $query   = Invoice::with(['order', 'party', 'createdBy', 'deliveryUser', 'items'])
            ->forRole($user)
            ->latest();

        if ($s = $request->get('status')) {
            $query->byStatus($s);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('party', fn ($sq) => $sq->where('store_name', 'like', "%{$search}%"))
                  ->orWhereHas('order', fn ($sq) => $sq->where('order_number', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->paginate(15)->withQueryString();
        $statuses = $this->validStatuses;
        $statusCounts = collect($this->validStatuses)->mapWithKeys(fn ($s) =>
            [$s => Invoice::forRole($user)->byStatus($s)->count()]
        )->toArray();
        $statusCounts['all'] = Invoice::forRole($user)->count();

        // Admin extras
        $deliveryUsers = $user?->role === 'admin'
            ? User::where('role', 'delivery')->orderBy('name')->get()
            : collect();

        return view('invoices.index', compact('invoices', 'statuses', 'statusCounts', 'deliveryUsers'));
    }

    public function create()
    {
        $this->authorizeRole(['admin', 'sales']);
        $parties  = Store::orderBy('store_name')->get();
        $products = \App\Models\Product::where('status', 'Active')->orderBy('name')->get();
        $nextNo   = Invoice::generateInvoiceNumber();
        return view('invoices.create', compact('parties', 'products', 'nextNo'));
    }

    public function storeWeb(Request $request)
    {
        $this->authorizeRole(['admin', 'sales']);
        $user = Auth::user();

        $validated = $request->validate([
            'party_id'    => 'nullable|exists:stores,id',
            'order_id'    => 'nullable|exists:orders,id',
            'date'        => 'nullable|date',
            'due_date'    => 'nullable|date',
            'tax'         => 'nullable|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string|max:1000',
            'items'       => 'nullable|array',
            'items.*.item_id'   => 'nullable|exists:products,id',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.quantity'  => 'required_with:items|numeric|min:0.01',
            'items.*.price'     => 'required_with:items|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $user, $request) {
            $items      = $validated['items'] ?? [];
            $subtotal   = collect($items)->sum(fn ($i) => ($i['quantity'] ?? 0) * ($i['price'] ?? 0));
            $tax        = (float) ($validated['tax'] ?? 0);
            $discount   = (float) ($validated['discount'] ?? 0);
            $amount     = $subtotal + $tax - $discount;

            $invoice = Invoice::create([
                'invoice_number'    => Invoice::generateInvoiceNumber(),
                'order_id'          => $validated['order_id'] ?? null,
                'party_id'          => $validated['party_id'] ?? null,
                'created_by'        => $user->id,
                'date'              => $validated['date'] ?? now()->toDateString(),
                'due_date'          => $validated['due_date'] ?? null,
                'amount'            => $amount,
                'tax'               => $tax,
                'discount'          => $discount,
                'status'            => 'pending',
                'notes'             => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $total = ($item['quantity'] ?? 0) * ($item['price'] ?? 0);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_id'    => $item['item_id'] ?? null,
                    'item_name'  => $item['item_name'] ?? null,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $total,
                ]);
            }
        });

        return redirect()->route('invoices.web.index')->with('success', 'Invoice created successfully.');
    }

    public function show($id)
    {
        $user    = Auth::user();
        $invoice = Invoice::with(['order', 'party', 'createdBy', 'deliveryUser', 'items.product'])
            ->forRole($user)
            ->findOrFail($id);

        $deliveryUsers = $user?->role === 'admin'
            ? User::where('role', 'delivery')->orderBy('name')->get()
            : collect();

        return view('invoices.show', compact('invoice', 'deliveryUsers'));
    }

    public function assignDelivery(Request $request, $id)
    {
        $this->authorizeRole(['admin']);
        $request->validate(['delivery_user_id' => 'required|exists:users,id']);

        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'assigned_delivery' => $request->delivery_user_id,
            'status'            => 'confirmed',
        ]);

        return back()->with('success', 'Delivery person assigned and invoice confirmed.');
    }

    public function updateStatus(Request $request, $id)
    {
        $user    = Auth::user();
        $invoice = Invoice::forRole($user)->findOrFail($id);

        $request->validate(['status' => 'required|in:' . implode(',', $this->validStatuses)]);
        $newStatus = $request->status;

        // Delivery can only move forward in allowed statuses
        if ($user?->role === 'delivery' && !in_array($newStatus, $this->deliveryAllowed)) {
            return back()->with('error', 'You are not allowed to set this status.');
        }

        // Sales cannot mark delivered
        if ($user?->role === 'sales' && $newStatus === 'delivered') {
            return back()->with('error', 'Sales cannot mark invoice as delivered.');
        }

        $invoice->update(['status' => $newStatus]);

        return back()->with('success', 'Status updated to ' . ucfirst($newStatus) . '.');
    }

    // ── Printable / Download ───────────────────────────────────────

    public function view($id)
    {
        $invoice = Invoice::with(['order', 'party', 'items.product', 'createdBy'])->findOrFail($id);
        return view('invoice.show', compact('invoice'));
    }

    public function download($id)
    {
        $invoice = Invoice::with(['order', 'party', 'items.product', 'createdBy'])->findOrFail($id);
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice.show', compact('invoice'));
            $filename = ($invoice->invoice_number ?: ('invoice-' . $invoice->id)) . '.pdf';
            return $pdf->download($filename);
        }
        return view('invoice.show', compact('invoice'));
    }

    // ── Legacy JSON endpoints (kept for backward compat) ──────────

    public function list()
    {
        $user = Auth::user();
        $data = Invoice::with(['order.items', 'items', 'party', 'createdBy', 'deliveryUser'])
            ->forRole($user)
            ->orderByDesc('id')
            ->get();
        return response()->json(['ok' => true, 'data' => $data]);
    }

    public function summary(Request $request)
    {
        $rows = Invoice::selectRaw(
            "DATE_FORMAT(COALESCE(date,created_at),'%Y-%m') as ym,
             COUNT(*) as issued,
             SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) as paid,
             SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending"
        )->groupBy('ym')->orderByDesc('ym')->get();

        $data = $rows->map(function ($r) {
            try { $month = Carbon::createFromFormat('Y-m', $r->ym)->format('M Y'); }
            catch (\Exception $e) { $month = $r->ym; }
            return ['month' => $month, 'issued' => (int) $r->issued, 'paid' => (int) $r->paid, 'pending' => (int) $r->pending];
        })->values();

        return response()->json(['ok' => true, 'data' => $data]);
    }

    public function destroy($id)
    {
        $this->authorizeRole(['admin']);
        Invoice::destroy($id);
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeRole(['admin', 'sales']);
        $v = Validator::make($request->all(), [
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $id,
            'order_id'       => 'nullable|exists:orders,id',
            'date'           => 'nullable|date',
            'amount'         => 'nullable|numeric',
            'status'         => 'required|string',
        ]);
        if ($v->fails()) return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        $invoice = Invoice::findOrFail($id);
        $invoice->update($v->validated());
        return response()->json(['ok' => true, 'data' => $invoice->load('order.items')]);
    }

    // ── API Endpoints ──────────────────────────────────────────────

    public function apiLogin(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $request->email)->first();
        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['ok' => false, 'message' => 'Invalid credentials.'], 401);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['ok' => true, 'token' => $token, 'role' => $user->role, 'user' => $user->only('id', 'name', 'email', 'role')]);
    }

    public function apiIndex(Request $request)
    {
        $user  = $request->user();
        $query = Invoice::with(['party', 'createdBy', 'deliveryUser', 'items'])
            ->forRole($user)
            ->latest();

        if ($s = $request->get('status')) $query->byStatus($s);

        return response()->json(['ok' => true, 'data' => $query->paginate(20)]);
    }

    public function apiShow(Request $request, $id)
    {
        $user    = $request->user();
        $invoice = Invoice::with(['party', 'createdBy', 'deliveryUser', 'items.product', 'order'])
            ->forRole($user)
            ->findOrFail($id);
        return response()->json(['ok' => true, 'data' => $invoice]);
    }

    public function apiStore(Request $request)
    {
        $user = $request->user();
        if (!in_array($user?->role, ['admin', 'sales'])) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized.'], 403);
        }

        $v = Validator::make($request->all(), [
            'party_id'  => 'nullable|exists:stores,id',
            'order_id'  => 'nullable|exists:orders,id',
            'date'      => 'nullable|date',
            'due_date'  => 'nullable|date',
            'tax'       => 'nullable|numeric|min:0',
            'discount'  => 'nullable|numeric|min:0',
            'notes'     => 'nullable|string',
            'items'     => 'nullable|array',
            'items.*.item_id'   => 'nullable|exists:products,id',
            'items.*.item_name' => 'nullable|string',
            'items.*.quantity'  => 'required_with:items|numeric|min:0.01',
            'items.*.price'     => 'required_with:items|numeric|min:0',
        ]);
        if ($v->fails()) return response()->json(['ok' => false, 'errors' => $v->errors()], 422);

        $invoice = DB::transaction(function () use ($v, $user) {
            $data     = $v->validated();
            $items    = $data['items'] ?? [];
            $subtotal = collect($items)->sum(fn ($i) => ($i['quantity'] ?? 0) * ($i['price'] ?? 0));
            $amount   = $subtotal + (float) ($data['tax'] ?? 0) - (float) ($data['discount'] ?? 0);

            $inv = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'order_id'       => $data['order_id'] ?? null,
                'party_id'       => $data['party_id'] ?? null,
                'created_by'     => $user->id,
                'date'           => $data['date'] ?? now()->toDateString(),
                'due_date'       => $data['due_date'] ?? null,
                'amount'         => $amount,
                'tax'            => $data['tax'] ?? 0,
                'discount'       => $data['discount'] ?? 0,
                'status'         => 'pending',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $inv->id,
                    'item_id'    => $item['item_id'] ?? null,
                    'item_name'  => $item['item_name'] ?? null,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => ($item['quantity'] ?? 0) * ($item['price'] ?? 0),
                ]);
            }
            return $inv->load('items');
        });

        return response()->json(['ok' => true, 'data' => $invoice], 201);
    }

    public function apiUpdateStatus(Request $request, $id)
    {
        $user    = $request->user();
        $invoice = Invoice::forRole($user)->findOrFail($id);

        $request->validate(['status' => 'required|in:' . implode(',', $this->validStatuses)]);
        $newStatus = $request->status;

        if ($user?->role === 'delivery' && !in_array($newStatus, $this->deliveryAllowed)) {
            return response()->json(['ok' => false, 'message' => 'Not allowed.'], 403);
        }
        if ($user?->role === 'sales' && $newStatus === 'delivered') {
            return response()->json(['ok' => false, 'message' => 'Sales cannot mark delivered.'], 403);
        }

        $invoice->update(['status' => $newStatus]);
        return response()->json(['ok' => true, 'data' => $invoice]);
    }

    public function apiAssignDelivery(Request $request, $id)
    {
        if ($request->user()?->role !== 'admin') {
            return response()->json(['ok' => false, 'message' => 'Admin only.'], 403);
        }
        $request->validate(['delivery_user_id' => 'required|exists:users,id']);
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['assigned_delivery' => $request->delivery_user_id, 'status' => 'confirmed']);
        return response()->json(['ok' => true, 'data' => $invoice->load('deliveryUser')]);
    }

    // ── Helpers ────────────────────────────────────────────────────

    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()?->role, $roles)) {
            abort(403, 'Unauthorized.');
        }
    }
}
