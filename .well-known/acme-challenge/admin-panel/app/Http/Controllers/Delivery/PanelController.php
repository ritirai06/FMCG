<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Attendance;
use App\Models\DeliveryPerson;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\Salary;
use App\Models\SalaryPayout;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    private array $deliveryStatuses = ['Assigned', 'Picked', 'Out for Delivery', 'Delivered', 'Failed', 'Returned'];

    private function hasUserColumn(string $column): bool
    {
        return Schema::hasColumn('users', $column);
    }

    private function hasRoleColumn(): bool
    {
        return $this->hasUserColumn('role');
    }

    private function hasStatusColumn(): bool
    {
        return $this->hasUserColumn('status');
    }

    private function isDeliveryUser(User $user): bool
    {
        if (!$this->hasRoleColumn()) {
            return true;
        }

        return $user->isDelivery();
    }

    private function userMobileColumn(): ?string
    {
        if (Schema::hasColumn('users', 'phone')) {
            return 'phone';
        }

        if (Schema::hasColumn('users', 'mobile')) {
            return 'mobile';
        }

        return null;
    }

    private function findDeliveryUserByPhone(string $phone): ?User
    {
        $mobileColumn = $this->userMobileColumn();

        if ($mobileColumn) {
            $query = User::where($mobileColumn, $phone);
            if ($this->hasRoleColumn()) {
                $query->where('role', 'delivery');
            }

            $user = $query->first();
            if ($user) {
                return $user;
            }
        }

        $deliveryPerson = DeliveryPerson::where('phone', $phone)->first();
        if ($deliveryPerson && !empty($deliveryPerson->email)) {
            $query = User::where('email', $deliveryPerson->email);
            if ($this->hasRoleColumn()) {
                $query->where('role', 'delivery');
            }

            return $query->first();
        }

        return null;
    }

    private function ensureDeliveryUser(): User
    {
        $user = Auth::user();
        if (!$user || !$this->isDeliveryUser($user)) {
            abort(403, 'Unauthorized');
        }

        return $user;
    }

    private function resolveDeliveryUserForPanel(): ?User
    {
        $user = Auth::user();
        if ($user && $this->isDeliveryUser($user)) {
            return $user;
        }

        $previewUser = User::query()
            ->when($this->hasRoleColumn(), fn ($q) => $q->where('role', 'delivery'))
            ->first();

        if ($previewUser) {
            Auth::login($previewUser);
            return $previewUser;
        }

        return null;
    }

    private function amountColumn(): string
    {
        return Schema::hasColumn('orders', 'total_amount') ? 'total_amount' : 'amount';
    }

    private function orderDeliveryAssignmentColumn(): ?string
    {
        foreach (['assigned_delivery', 'delivery_user_id', 'delivery_id'] as $column) {
            if (Schema::hasColumn('orders', $column)) {
                return $column;
            }
        }

        return null;
    }

    private function scopeOrdersForDeliveryUser($query, int $userId)
    {
        $column = $this->orderDeliveryAssignmentColumn();
        if (!$column) {
            // If explicit delivery assignment column does not exist, at least
            // limit to delivery-lifecycle orders (exclude cancelled/non-delivery noise).
            return $query->whereIn('status', [
                'Pending', 'Assigned', 'Picked', 'Processing', 'Out for Delivery', 'Delivered', 'Completed', 'Failed', 'Returned',
                'pending', 'assigned', 'picked', 'processing', 'out for delivery', 'out_for_delivery',
                'delivered', 'completed', 'failed', 'returned',
            ]);
        }

        return $query->where($column, $userId);
    }

    private function orderAssignedDeliveryUserId(Order $order): ?int
    {
        $column = $this->orderDeliveryAssignmentColumn();
        if (!$column) {
            return null;
        }

        $value = data_get($order, $column);
        return $value !== null ? (int) $value : null;
    }

    private function canDeliveryUserAccessOrder(User $user, Order $order): bool
    {
        $column = $this->orderDeliveryAssignmentColumn();

        // If assignment column does not exist, follow scoped delivery flow
        // and do not hard-fail with 403 on status actions from the panel.
        if (!$column) {
            return true;
        }

        $assignedTo = data_get($order, $column);
        return $assignedTo !== null && (int) $assignedTo === (int) $user->id;
    }

    private function baseContext(): array
    {
        $user = Auth::user();
        $companySettings = AdminSetting::first();
        $companyName = 'FMCG';

        $deliveryProfile = null;
        if ($user) {
            $deliveryProfile = DeliveryPerson::where('email', $user->email)->first();
            if (!$deliveryProfile && Schema::hasColumn('users', 'phone') && $user->phone) {
                $deliveryProfile = DeliveryPerson::where('phone', $user->phone)->first();
            }
        }

        return [
            'user' => $user,
            'companySettings' => $companySettings,
            'companyName' => $companyName,
            'deliveryProfile' => $deliveryProfile,
        ];
    }

    public function showLogin()
    {
        if (Auth::check() && $this->isDeliveryUser(Auth::user())) {
            return redirect()->route('delivery.panel.dashboard');
        }

        return view('delivery_panel.login');
    }

    public function showRegister()
    {
        if (Auth::check() && $this->isDeliveryUser(Auth::user())) {
            return redirect()->route('delivery.panel.dashboard');
        }

        return view('delivery_panel.register');
    }

    public function showOtpVerify(Request $request)
    {
        if (Auth::check() && Auth::user()->isDelivery()) {
            return redirect()->route('delivery.panel.dashboard');
        }

        $phone = $request->get('phone');

        return view('delivery_panel.otp_verify', compact('phone'));
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
        ]);

        $phone = $request->phone;
        $user = $this->findDeliveryUserByPhone($phone);

        if (!$user) {
            return back()->with('error', 'Delivery user not found for this mobile number.')->withInput();
        }

        $otp = random_int(100000, 999999);
        Cache::put('delivery_otp:' . $phone, $otp, now()->addMinutes(5));
        Log::info('Delivery OTP for ' . $phone . ': ' . $otp);

        return redirect()
            ->route('delivery.panel.otp.verify.page', ['phone' => $phone])
            ->with('success', 'OTP sent successfully. (Dev: check logs)');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:6',
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $cachedOtp = Cache::get('delivery_otp:' . $phone);
        if (!$cachedOtp || (string) $cachedOtp !== (string) $otp) {
            return back()->with('error', 'Invalid or expired OTP.')->withInput();
        }

        $user = $this->findDeliveryUserByPhone($phone);
        if (!$user) {
            return back()->with('error', 'Delivery user not found.')->withInput();
        }

        Cache::forget('delivery_otp:' . $phone);
        Auth::login($user);

        return redirect()->route('delivery.panel.dashboard');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            !$user ||
            !Hash::check($request->password, $user->password) ||
            !$this->isDeliveryUser($user) ||
            ($this->hasStatusColumn() && isset($user->status) && !$user->status)
        ) {
            return back()->withInput()->with('error', 'Invalid delivery credentials.');
        }

        Auth::login($user);

        return redirect()->route('delivery.panel.dashboard');
    }

    public function register(Request $request)
    {
        $mobileColumn = $this->userMobileColumn();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];

        $rules['phone'] = $mobileColumn
            ? 'required|digits:10|unique:users,' . $mobileColumn
            : 'required|digits:10';

        $request->validate($rules);

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($mobileColumn) {
            $payload[$mobileColumn] = $request->phone;
        }

        if ($this->hasRoleColumn()) {
            $payload['role'] = 'delivery';
        }

        if ($this->hasStatusColumn()) {
            $payload['status'] = true;
        }

        User::create($payload);

        return redirect()
            ->route('delivery.panel.login')
            ->with('success', 'Registration successful. Please login to continue.');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('delivery.panel.login');
    }

    public function dashboard()
    {
        $user = $this->ensureDeliveryUser();
        $context = $this->baseContext();

        $amountColumn = $this->amountColumn();
        $base = $this->scopeOrdersForDeliveryUser(Order::query(), $user->id);
        $today = now()->toDateString();

        $pendingStatuses = ['Pending', 'Assigned', 'Picked', 'pending', 'assigned', 'picked'];
        $outForDeliveryStatuses = ['Out for Delivery', 'out for delivery', 'Out For Delivery', 'out_for_delivery'];
        $deliveredStatuses = ['Delivered', 'delivered', 'Completed', 'completed'];
        $failedReturnedStatuses = ['Failed', 'Returned', 'failed', 'returned'];

        $stats = [
            'total_assigned' => (clone $base)->count(),
            'pending' => (clone $base)->whereIn('status', $pendingStatuses)->count(),
            'out_for_delivery' => (clone $base)->whereIn('status', $outForDeliveryStatuses)->count(),
            'delivered' => (clone $base)->whereIn('status', $deliveredStatuses)->count(),
            'failed_or_returned' => (clone $base)->whereIn('status', $failedReturnedStatuses)->count(),
            'today_delivered' => (clone $base)->whereIn('status', $deliveredStatuses)->whereDate('updated_at', $today)->count(),
            'today_assigned' => (clone $base)->whereDate('created_at', $today)->count(),
            'today_revenue' => (clone $base)->whereDate('created_at', $today)->sum($amountColumn) ?? 0,
            'total_revenue' => (clone $base)->sum($amountColumn) ?? 0,
        ];

        $recentOrders = (clone $base)
            ->with('store')
            ->latest('created_at')
            ->take(8)
            ->get();

        $statusBreakdown = collect($this->deliveryStatuses)
            ->mapWithKeys(fn (string $status) => [$status => (clone $base)->where('status', $status)->count()])
            ->toArray();

        $pendingOrders = (clone $base)
            ->with('store')
            ->whereNotIn('status', ['Delivered', 'Failed', 'Returned', 'Cancelled', 'delivered', 'failed', 'returned', 'cancelled', 'Completed', 'completed'])
            ->latest('created_at')
            ->take(6)
            ->get();

        $assignedStoresCount = Store::whereIn('id', (clone $base)->whereNotNull('store_id')->pluck('store_id')->unique())
            ->count();

        return view('delivery_panel.dashboard', array_merge(
            $context,
            compact('stats', 'recentOrders', 'statusBreakdown', 'pendingOrders', 'assignedStoresCount')
        ));
    }

    public function orders(Request $request)
    {
        $user = $this->ensureDeliveryUser();
        $context = $this->baseContext();

        $query = $this->scopeOrdersForDeliveryUser(
            Order::with(['store', 'createdBy']),
            $user->id
        );

        $filters = [
            'status' => $request->get('status', 'all'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $orders = $query->latest('created_at')->paginate(12)->withQueryString();
        $statuses = $this->deliveryStatuses;

        return view('delivery_panel.orders', array_merge($context, compact('orders', 'filters', 'statuses')));
    }

    public function myOrders(Request $request)
    {
        $user = $this->resolveDeliveryUserForPanel();
        $context = $this->baseContext();
        $query = Order::with(['store', 'createdBy', 'assignedDelivery']);

        if ($user && $this->isDeliveryUser($user)) {
            $query = $this->scopeOrdersForDeliveryUser($query, $user->id);
        }

        $filters = [
            'status' => $request->get('status', 'all'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $orders = (clone $query)->latest('created_at')->paginate(12)->withQueryString();
        $statuses = $this->deliveryStatuses;
        $statusOrders = [
            'assigned' => (clone $query)->where('status', 'Assigned')->latest('updated_at')->get(),
            'picked' => (clone $query)->where('status', 'Picked')->latest('updated_at')->get(),
            'out_for_delivery' => (clone $query)->where('status', 'Out for Delivery')->latest('updated_at')->get(),
            'delivered' => (clone $query)->where('status', 'Delivered')->latest('updated_at')->get(),
            'failed' => (clone $query)->whereIn('status', ['Failed', 'Returned'])->latest('updated_at')->get(),
        ];
        $statusCounts = collect($statusOrders)->map(fn ($items) => $items->count())->toArray();

        return view('delivery_panel.my_orders.index', array_merge(
            $context,
            compact('orders', 'filters', 'statuses', 'user', 'statusOrders', 'statusCounts')
        ));
    }

    public function orderDetails($id = null)
    {
        $user = $this->resolveDeliveryUserForPanel();
        $context = $this->baseContext();
        $ordersBase = Order::query()
            ->when($user && $this->isDeliveryUser($user), fn ($q) => $this->scopeOrdersForDeliveryUser($q, $user->id));

        if (!$id) {
            $fallbackOrder = (clone $ordersBase)
                ->where('status', 'Picked')
                ->latest('updated_at')
                ->first();

            if (!$fallbackOrder) {
                $fallbackOrder = (clone $ordersBase)->latest('updated_at')->first();
            }

            if (!$fallbackOrder) {
                return redirect()->route('delivery.panel.my.orders')
                    ->with('error', 'No order available for details.');
            }

            return redirect()->route('delivery.panel.order.details', ['id' => $fallbackOrder->id]);
        }

        // Fetch order with relationships
        $order = Order::with(['store', 'createdBy', 'assignedDelivery', 'items', 'items.product'])
            ->findOrFail($id);

        // Check authorization - order must be assigned to current user.
        // If not assigned, redirect to an allowed order instead of showing 403.
        if (
            $user &&
            $this->isDeliveryUser($user) &&
            !$this->canDeliveryUserAccessOrder($user, $order)
        ) {
            $fallbackOrder = (clone $ordersBase)
                ->where('status', 'Picked')
                ->latest('updated_at')
                ->first();

            if (!$fallbackOrder) {
                $fallbackOrder = (clone $ordersBase)->latest('updated_at')->first();
            }

            if ($fallbackOrder) {
                return redirect()
                    ->route('delivery.panel.order.details', ['id' => $fallbackOrder->id])
                    ->with('error', 'You are not authorized to view that order. Showing your assigned order instead.');
            }

            return redirect()
                ->route('delivery.panel.my.orders')
                ->with('error', 'No authorized order found for your account.');
        }

        $recentOrders = Order::query()
            ->when($user && $this->isDeliveryUser($user), fn ($q) => $this->scopeOrdersForDeliveryUser($q, $user->id))
            ->with('store')
            ->latest('created_at')
            ->take(8)
            ->get();

        $orderNavigator = (clone $ordersBase)
            ->with('store')
            ->latest('updated_at')
            ->take(100)
            ->get();

        $statusTimeline = collect([
            [
                'label' => 'Order Created',
                'time' => $order->created_at,
                'description' => 'Order placed and registered in system.',
                'badge' => 'primary',
                'icon' => 'fa-check-circle',
            ],
            $this->orderAssignedDeliveryUserId($order) ? [
                'label' => 'Assigned to Delivery Person',
                'time' => $order->updated_at,
                'description' => 'Assigned to: ' . data_get($order, 'assignedDelivery.name', ($user?->name ?? 'Delivery Person')),
                'badge' => 'info',
                'icon' => 'fa-user-check',
            ] : null,
            [
                'label' => (string) ($order->status ?? 'Order Status Updated'),
                'time' => $order->updated_at,
                'description' => 'Current status: ' . (string) ($order->status ?? 'Pending'),
                'badge' => match ((string) ($order->status ?? '')) {
                    'Delivered' => 'success',
                    'Out for Delivery' => 'warning',
                    'Failed', 'Returned' => 'danger',
                    default => 'secondary',
                },
                'icon' => match ((string) ($order->status ?? '')) {
                    'Delivered' => 'fa-check-double',
                    'Out for Delivery' => 'fa-truck',
                    'Failed', 'Returned' => 'fa-times',
                    default => 'fa-circle',
                },
            ],
            !empty($order->notes) ? [
                'label' => 'Delivery Notes',
                'time' => $order->updated_at,
                'description' => (string) $order->notes,
                'badge' => 'dark',
                'icon' => 'fa-sticky-note',
            ] : null,
        ])->filter()->values();

        return view('delivery_panel.order_details.details', array_merge(
            $context,
            compact('order', 'recentOrders', 'statusTimeline', 'orderNavigator')
        ));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $user = $this->ensureDeliveryUser();

        if (!$this->canDeliveryUserAccessOrder($user, $order)) {
            return redirect()
                ->route('delivery.panel.my.orders')
                ->with('error', 'You are not assigned to this order.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', $this->deliveryStatuses),
            'notes' => 'nullable|string|max:1000',
            'failure_reason' => 'nullable|string|max:255',
        ]);

        $notes = trim((string) ($validated['notes'] ?? ''));
        $failureReason = trim((string) ($validated['failure_reason'] ?? ''));
        if ($failureReason !== '') {
            $notes = $notes !== ''
                ? ($notes . ' | Reason: ' . $failureReason)
                : ('Reason: ' . $failureReason);
        }

        $previousStatus = (string) $order->status;
        $nextStatus = (string) $validated['status'];

        DB::transaction(function () use ($order, $previousStatus, $nextStatus, $notes) {
            $updateData = [
                'status' => $nextStatus,
            ];

            // Some deployments still don't have orders.notes column.
            if (Schema::hasColumn('orders', 'notes')) {
                $updateData['notes'] = $notes !== '' ? $notes : $order->notes;
            }

            $order->update($updateData);

            // Real inventory flow: deduct stock only when transitioning into Delivered.
            if ($previousStatus !== 'Delivered' && $nextStatus === 'Delivered') {
                $order->loadMissing('items');
                foreach ($order->items as $item) {
                    $productId = $item->product_id;
                    $qty = (int) ($item->quantity ?? 0);
                    if (!$productId || $qty <= 0) {
                        continue;
                    }

                    $product = Product::find($productId);
                    if ($product) {
                        $currentStock = (int) ($product->stock_quantity ?? 0);
                        $product->stock_quantity = max(0, $currentStock - $qty);
                        $product->save();
                    }

                    $inventory = Inventory::where('product_id', $productId)->first();
                    if ($inventory) {
                        $inventory->quantity = max(0, (int) $inventory->quantity - $qty);
                        $inventory->save();

                        InventoryLog::create([
                            'inventory_id' => $inventory->id,
                            'type' => 'out',
                            'quantity' => $qty,
                            'note' => 'Order ' . ($order->order_number ?? ('#' . $order->id)) . ' delivered by delivery panel',
                        ]);
                    }
                }
            }
        });

        $successMessage = 'Order status updated to ' . $validated['status'] . '.';
        $isModalRequest = $request->boolean('modal');

        // Professional flow:
        // If current order is completed/failed/returned, move delivery partner to next actionable order.
        if (in_array($nextStatus, ['Delivered', 'Failed', 'Returned'], true)) {
            $nextActionableOrder = $this->scopeOrdersForDeliveryUser(Order::query(), $user->id)
                ->where('id', '!=', $order->id)
                ->whereIn('status', ['Assigned', 'Pending', 'Picked', 'Out for Delivery'])
                ->orderByRaw("CASE status
                    WHEN 'Out for Delivery' THEN 1
                    WHEN 'Picked' THEN 2
                    WHEN 'Assigned' THEN 3
                    WHEN 'Pending' THEN 4
                    ELSE 9
                END")
                ->orderBy('updated_at')
                ->first();

            if ($nextActionableOrder) {
                return redirect()
                    ->route('delivery.panel.order.details', array_filter([
                        'id' => $nextActionableOrder->id,
                        'modal' => $isModalRequest ? 1 : null,
                    ]))
                    ->with('success', $successMessage . ' Next order opened for processing.');
            }

            if ($isModalRequest) {
                return redirect()
                    ->route('delivery.panel.order.details', ['id' => $order->id, 'modal' => 1])
                    ->with('success', $successMessage . ' No more actionable orders pending.');
            }

            return redirect()
                ->route('delivery.panel.my.orders')
                ->with('success', $successMessage . ' No more actionable orders pending.');
        }

        return redirect()
            ->route('delivery.panel.order.details', array_filter([
                'id' => $order->id,
                'modal' => $isModalRequest ? 1 : null,
            ]))
            ->with('success', $successMessage);
    }

    public function stores()
    {
        $user = $this->ensureDeliveryUser();
        $context = $this->baseContext();

        $storeIds = $this->scopeOrdersForDeliveryUser(Order::query(), $user->id)
            ->whereNotNull('store_id')
            ->pluck('store_id')
            ->unique()
            ->values();

        $stores = Store::whereIn('id', $storeIds)
            ->withCount([
                'orders as assigned_orders_count' => function ($q) use ($user) {
                    $this->scopeOrdersForDeliveryUser($q, $user->id);
                },
                'orders as delivered_orders_count' => function ($q) use ($user) {
                    $this->scopeOrdersForDeliveryUser($q, $user->id)->where('status', 'Delivered');
                },
            ])
            ->orderBy('store_name')
            ->get();

        return view('delivery_panel.stores', array_merge($context, compact('stores')));
    }

    public function attendance(Request $request)
    {
        $user = $this->ensureDeliveryUser();
        return $this->renderAttendancePage($request, $user);
    }

    public function attendancePreview(Request $request)
    {
        [$month, $year] = $this->resolveAttendancePeriod($request);
        $user = Auth::user();
        if (!$user) {
            $user = User::query()
                ->when(Schema::hasColumn('users', 'role'), fn ($q) => $q->where('role', 'delivery'))
                ->first();
        }
        if (!$user) {
            $user = User::query()->first();
        }
        if (!$user) {
            $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();

            $calendarDays = collect();
            for ($day = 1; $day <= $startOfMonth->daysInMonth; $day++) {
                $date = Carbon::create($year, $month, $day);
                $calendarDays->push([
                    'date' => $date,
                    'status' => 'Absent',
                    'deliveries_count' => 0,
                ]);
            }

            $context = $this->baseContext();

            return view('delivery_panel.attendance.index', array_merge($context, [
                'records' => collect(),
                'summary' => [
                    'present' => 0,
                    'absent' => $calendarDays->count(),
                    'late' => 0,
                    'leave' => 0,
                    'completed_deliveries' => 0,
                ],
                'month' => $month,
                'year' => $year,
                'calendarDays' => $calendarDays,
                'todayStatus' => 'Absent',
                'todayCompletedDeliveries' => 0,
            ]));
        }

        return $this->renderAttendancePage($request, $user);
    }

    public function markAttendance(Request $request)
    {
        $user = $this->ensureDeliveryUser();
        $context = $this->baseContext();

        $validated = $request->validate([
            'status' => 'required|string|in:Present,Late,Leave,Absent',
            'notes' => 'nullable|string|max:500',
            'action_type' => 'nullable|string|in:check_in,check_out,update',
        ]);

        $employeeName = trim((string) (
            data_get($context, 'deliveryProfile.name')
            ?: $user->name
            ?: 'Delivery Partner'
        ));

        $today = now()->toDateString();
        $actionType = (string) ($validated['action_type'] ?? 'update');

        $attendance = Attendance::firstOrNew([
            'employee_name' => $employeeName,
            'date' => $today,
        ]);

        $attendance->status = $validated['status'];
        $attendance->notes = $validated['notes'] ?? $attendance->notes;

        if ($actionType === 'check_in' && empty($attendance->time_in)) {
            $attendance->time_in = now()->format('H:i:s');
        }
        if ($actionType === 'check_out') {
            if (empty($attendance->time_in)) {
                $attendance->time_in = now()->format('H:i:s');
            }
            $attendance->time_out = now()->format('H:i:s');
        }

        $attendance->save();

        return redirect()
            ->route('delivery.panel.attendance', [
                'month' => now()->month,
                'year' => now()->year,
            ])
            ->with('success', 'Attendance updated successfully.');
    }

    private function renderAttendancePage(Request $request, User $user)
    {
        if (Auth::check() && Auth::id() !== $user->id) {
            Auth::logout();
        }
        if (!Auth::check()) {
            Auth::login($user);
        }

        $context = $this->baseContext();
        [$month, $year] = $this->resolveAttendancePeriod($request);

        $attendanceNameCandidates = collect([
            $user->name,
            data_get($context, 'deliveryProfile.name'),
        ])
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values();

        $attendanceBase = Attendance::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->when($attendanceNameCandidates->isNotEmpty(), function ($query) use ($attendanceNameCandidates) {
                $query->whereIn('employee_name', $attendanceNameCandidates->all());
            });

        if ((clone $attendanceBase)->count() === 0) {
            $attendanceBase = Attendance::query()
                ->whereMonth('date', $month)
                ->whereYear('date', $year);
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $completedDeliveriesByDay = $this->scopeOrdersForDeliveryUser(Order::query(), $user->id)
            ->where('status', 'Delivered')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(updated_at) as day, COUNT(*) as deliveries_count')
            ->groupBy('day')
            ->pluck('deliveries_count', 'day');

        // Keep attendance calendar fully dynamic and persisted in DB for delivery flow.
        $employeeName = $attendanceNameCandidates->first() ?? trim((string) ($user->name ?: 'Delivery Partner'));
        if ($employeeName !== '') {
            $todayDate = now()->startOfDay();
            for ($day = 1; $day <= $startOfMonth->daysInMonth; $day++) {
                $date = Carbon::create($year, $month, $day)->startOfDay();

                // Don't pre-mark future dates in current/future months.
                if ($date->gt($todayDate)) {
                    continue;
                }

                $dateKey = $date->toDateString();
                $deliveriesCount = (int) ($completedDeliveriesByDay[$dateKey] ?? 0);
                $defaultStatus = $deliveriesCount > 0 ? 'Present' : 'Absent';

                Attendance::firstOrCreate(
                    [
                        'employee_name' => $employeeName,
                        'date' => $dateKey,
                    ],
                    [
                        'status' => $defaultStatus,
                        'notes' => 'Auto-generated from delivery activity.',
                    ]
                );
            }
        }

        $attendanceBase = Attendance::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->when($attendanceNameCandidates->isNotEmpty(), function ($query) use ($attendanceNameCandidates, $employeeName) {
                $query->whereIn('employee_name', $attendanceNameCandidates->push($employeeName)->filter()->unique()->values()->all());
            });

        $records = (clone $attendanceBase)
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        $records->getCollection()->transform(function ($record) use ($completedDeliveriesByDay) {
            $dateKey = Carbon::parse($record->date)->toDateString();
            $record->completed_orders = (int) ($completedDeliveriesByDay[$dateKey] ?? 0);

            return $record;
        });

        $manualAttendanceByDay = (clone $attendanceBase)
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->date)->toDateString());

        $calendarDays = collect();
        for ($day = 1; $day <= $startOfMonth->daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dateKey = $date->toDateString();
            $deliveriesCount = (int) ($completedDeliveriesByDay[$dateKey] ?? 0);
            $manualStatus = $manualAttendanceByDay[$dateKey]->status ?? null;

            $status = $manualStatus ?: ($deliveriesCount > 0 ? 'Present' : 'Absent');
            $calendarDays->push([
                'date' => $date,
                'status' => $status,
                'deliveries_count' => $deliveriesCount,
            ]);
        }

        $summary = [
            'present' => $calendarDays->where('status', 'Present')->count(),
            'absent' => $calendarDays->where('status', 'Absent')->count(),
            'late' => $calendarDays->where('status', 'Late')->count(),
            'leave' => $calendarDays->where('status', 'Leave')->count(),
            'completed_deliveries' => (int) $completedDeliveriesByDay->sum(),
        ];

        $today = now()->toDateString();
        $todayStatusData = $calendarDays->first(fn ($d) => $d['date']->toDateString() === $today);
        $todayStatus = $todayStatusData['status'] ?? 'Absent';
        $todayCompletedDeliveries = $todayStatusData['deliveries_count'] ?? 0;
        $todayAttendance = $manualAttendanceByDay[$today] ?? null;

        return view('delivery_panel.attendance.index', array_merge($context, compact(
            'records',
            'summary',
            'month',
            'year',
            'calendarDays',
            'todayStatus',
            'todayCompletedDeliveries',
            'todayAttendance'
        )));
    }

    private function resolveAttendancePeriod(Request $request): array
    {
        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;

        $month = (int) $request->get('month', $currentMonth);
        $year = (int) $request->get('year', $currentYear);

        if ($month < 1 || $month > 12) {
            $month = $currentMonth;
        }

        if ($year < 2000 || $year > ($currentYear + 2)) {
            $year = $currentYear;
        }

        return [$month, $year];
    }

    public function earningsPreview(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $user = User::query()
                ->when(Schema::hasColumn('users', 'role'), fn ($q) => $q->where('role', 'delivery'))
                ->first();
        }
        if (!$user) {
            $user = User::query()->first();
        }

        return $this->renderEarningsPage($request, $user);
    }

    private function renderEarningsPage(Request $request, ?User $user)
    {
        if ($user && Auth::check() && Auth::id() !== $user->id) {
            Auth::logout();
        }
        if ($user && !Auth::check()) {
            Auth::login($user);
        }

        $context = $this->baseContext();
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $baseSalary = 0.0;
        $allowances = 0.0;
        $monthlySalary = 0.0;
        $incentives = 0.0;
        $deliveryCommission = 0.0;
        $deliveredCount = 0;
        $deliveredAmount = 0.0;
        $payoutHistory = collect();
        $performanceData = collect();

        if ($user) {
            if (Schema::hasTable('salaries')) {
                $salaryQuery = Salary::query()->where('employee_name', $user->name);
                if (Schema::hasColumn('salaries', 'role')) {
                    $salaryQuery->where(function ($q) {
                        $q->where('role', 'delivery')->orWhereNull('role');
                    });
                }
                $salaryRow = $salaryQuery->latest('id')->first();
                $baseSalary = (float) ($salaryRow->base_salary ?? 0);
                $allowances = (float) ($salaryRow->allowances ?? 0);
            }

            $amountColumn = $this->amountColumn();
            $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

            $monthDeliveredBase = $this->scopeOrdersForDeliveryUser(Order::query(), $user->id)
                ->where('status', 'Delivered')
                ->whereBetween('updated_at', [$monthStart, $monthEnd]);

            $deliveredCount = (clone $monthDeliveredBase)->count();
            $deliveredAmount = (float) ((clone $monthDeliveredBase)->sum($amountColumn) ?? 0);

            if (Schema::hasTable('salary_payouts')) {
                $payoutBase = SalaryPayout::query()
                    ->where('employee_name', $user->name)
                    ->orderByDesc('year')
                    ->orderByDesc('month')
                    ->orderByDesc('id');

                $currentPayout = (clone $payoutBase)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                $incentives = (float) ($currentPayout->incentive ?? 0);
                $monthlySalary = (float) ($currentPayout->total_payout ?? ($baseSalary + $allowances));
                $deliveryCommission = max(
                    0,
                    (float) ($currentPayout->total_payout ?? 0)
                    - (float) ($currentPayout->base_salary ?? $baseSalary)
                    - (float) ($currentPayout->allowances ?? $allowances)
                    - (float) ($currentPayout->incentive ?? 0)
                );

                $payoutHistory = $payoutBase->paginate(10)->withQueryString();
            } else {
                $monthlySalary = $baseSalary + $allowances;
                $payoutHistory = collect();
            }

            $performanceData = collect(range(0, 5))
                ->map(function ($i) use ($user, $amountColumn) {
                    $date = now()->subMonths(5 - $i);
                    $start = $date->copy()->startOfMonth();
                    $end = $date->copy()->endOfMonth();

                    $orders = $this->scopeOrdersForDeliveryUser(Order::query(), $user->id)
                        ->where('status', 'Delivered')
                        ->whereBetween('updated_at', [$start, $end]);

                    return [
                        'label' => $date->format('M Y'),
                        'deliveries' => (int) (clone $orders)->count(),
                        'amount' => (float) ((clone $orders)->sum($amountColumn) ?? 0),
                    ];
                });
        }

        return view('delivery_panel.Incentives.index', array_merge($context, compact(
            'month',
            'year',
            'baseSalary',
            'allowances',
            'monthlySalary',
            'incentives',
            'deliveryCommission',
            'deliveredCount',
            'deliveredAmount',
            'payoutHistory',
            'performanceData'
        )));
    }

    public function profile()
    {
        $user = $this->ensureDeliveryUser();
        return $this->renderProfilePage($user);
    }

    public function profilePreview()
    {
        $user = Auth::user();
        if (!$user) {
            $user = User::query()
                ->when(Schema::hasColumn('users', 'role'), fn ($q) => $q->where('role', 'delivery'))
                ->first();
        }
        if (!$user) {
            $user = User::query()->first();
        }

        return $this->renderProfilePage($user);
    }

    public function updateProfile(Request $request)
    {
        $user = $this->ensureDeliveryUser();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|digits:10',
            'zones' => 'nullable|string|max:1000',
        ]);

        $mobileColumn = $this->userMobileColumn();
        $newPhone = $validated['phone'] ?? null;

        $user->name = $validated['name'];
        if ($mobileColumn) {
            $user->{$mobileColumn} = $newPhone;
        }
        $user->save();

        $deliveryProfileQuery = DeliveryPerson::query();
        $hasProfileFilter = false;
        if (!empty($user->email)) {
            $deliveryProfileQuery->where('email', $user->email);
            $hasProfileFilter = true;
        }
        if ($mobileColumn && !empty($user->{$mobileColumn})) {
            $method = $hasProfileFilter ? 'orWhere' : 'where';
            $deliveryProfileQuery->{$method}('phone', $user->{$mobileColumn});
            $hasProfileFilter = true;
        }

        $deliveryProfile = $hasProfileFilter ? $deliveryProfileQuery->first() : null;

        if (!$deliveryProfile && (!empty($user->email) || !empty($newPhone))) {
            $deliveryProfile = new DeliveryPerson();
            $deliveryProfile->email = $user->email;
        }

        if ($deliveryProfile) {
            $deliveryProfile->name = $validated['name'];
            if ($newPhone !== null) {
                $deliveryProfile->phone = $newPhone;
            }

            $zones = collect(explode(',', (string) ($validated['zones'] ?? '')))
                ->map(fn ($zone) => trim($zone))
                ->filter(fn ($zone) => $zone !== '')
                ->values()
                ->all();

            $deliveryProfile->zones_json = $zones;
            $deliveryProfile->save();
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = $this->ensureDeliveryUser();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed|different:current_password',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    private function renderProfilePage(?User $user)
    {
        if ($user && Auth::check() && Auth::id() !== $user->id) {
            Auth::logout();
        }
        if ($user && !Auth::check()) {
            Auth::login($user);
        }

        $context = $this->baseContext();
        $deliveryProfile = $context['deliveryProfile'] ?? null;

        $profileUser = $user ?? Auth::user();
        $phone = $deliveryProfile?->phone
            ?? (Schema::hasColumn('users', 'phone') ? ($profileUser?->phone ?? null) : null)
            ?? (Schema::hasColumn('users', 'mobile') ? ($profileUser?->mobile ?? null) : null);

        $zones = collect($deliveryProfile?->zones_json ?? [])
            ->filter(fn ($zone) => filled($zone))
            ->values();

        $amountColumn = $this->amountColumn();
        $base = $profileUser
            ? $this->scopeOrdersForDeliveryUser(Order::query(), $profileUser->id)
            : Order::query()->whereRaw('1 = 0');

        $profileStats = [
            'total_assigned' => (clone $base)->count(),
            'delivered' => (clone $base)->where('status', 'Delivered')->count(),
            'out_for_delivery' => (clone $base)->where('status', 'Out for Delivery')->count(),
            'revenue_handled' => (clone $base)->sum($amountColumn) ?? 0,
        ];

        return view('delivery_panel.profile.index', array_merge($context, [
            'profileUser' => $profileUser,
            'phone' => $phone,
            'zones' => $zones,
            'profileStats' => $profileStats,
        ]));
    }
}
