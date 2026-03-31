# 📋 Professional Order List Module - Complete Implementation

## 🎯 Overview

This document provides complete implementation details for the professional Order List Module with **role-based access control**, **advanced filtering**, and **status management**. 

---

## 📦 Components Created/Updated

### 1. **Routes** (`routes/web.php`)
```php
Route::middleware(['auth'])->prefix('orders')->group(function(){
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/create', [OrderController::class, 'create'])->name('orders.create')->middleware('role:admin,sales');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store')->middleware('role:admin,sales');
    Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit')->middleware('role:admin');
    Route::put('/{order}', [OrderController::class, 'update'])->name('orders.update')->middleware('role:admin');
    Route::delete('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
```

**Key Features:**
- ✅ Role-based middleware protection
- ✅ RESTful resource routing
- ✅ Separate AJAX status endpoint
- ✅ Proper HTTP verbs (GET, POST, PUT, DELETE)

---

### 2. **Form Request** (`app/Http/Requests/UpdateOrderStatusRequest.php`)

**Purpose:** Server-side validation for order status updates

**Validation Rules:**
```php
'status' => 'required|in:Pending,Approved,Packed,Out for Delivery,Delivered,Cancelled'
'assigned_delivery' => 'nullable|exists:users,id'
'notes' => 'nullable|string|max:1000'
```

**Custom Error Messages:**
- Status is required
- Invalid status provided
- Delivery person must exist in database

---

### 3. **Order Service** (`app/Services/OrderListService.php`)

**Responsibility:** Encapsulate all order filtering and business logic

#### Key Methods:

**`getFilteredOrders(array $filters = [], int $perPage = 15)`**
- Applies role-based filtering
- Applies additional filters (status, store, date range)
- Returns paginated results

**`applyRoleFilter($query, User $user)`**
- **Admin:** Can see all orders
- **Sales:** Can see only their created orders (`created_by = auth()->id()`)
- **Delivery:** Can see only assigned orders (`assigned_delivery = auth()->id()`)

**`applyStatusFilter($query, array $filters)`**
- Filters by order status
- Supports: Pending, Approved, Packed, Out for Delivery, Delivered, Cancelled

**`applyStoreFilter($query, array $filters)`**
- Filters by store_id
- Admin/Sales can filter, Delivery cannot

**`applyDateRangeFilter($query, array $filters)`**
- Filters by `date_from` and `date_to`
- Applied on `created_at` timestamp

**`getOrdersSummary(): array`**
- Returns count of orders by status
- Includes total revenue
- Role-aware (Sales/Delivery see only their orders)

**`getAvailableStatuses(): array`**
- Returns OrderHelper::getOrderStatuses()
- Used for dropdown filters

**`canTransitionStatus(Order $order, string $newStatus): bool`**
- Validates role-based status transitions
- Prevents unauthorized status changes

---

### 4. **Order Model** (`app/Models/Order.php`)

**New Scopes Added:**

```php
// Filter by date range
scopeByDateRange($fromDate, $toDate = null)

// Filter by store
scopeByStore($storeId)

// Convenience scopes
scopeToday()
scopeThisMonth()
scopeDelivered()
scopePending()
scopeWithDeliveryAssigned()
```

**Example Usage:**
```php
Order::byStatus('Pending')
    ->byStore(1)
    ->byDateRange('2026-01-01', '2026-01-31')
    ->get();
```

---

### 5. **OrderController** (`app/Http/Controllers/OrderController.php`)

#### **Enhanced `index()` Method**

```php
public function index(Request $request)
{
    $filters = [
        'status' => $request->get('status', 'all'),
        'store' => $request->get('store', 'all'),
        'date_from' => $request->get('date_from'),
        'date_to' => $request->get('date_to'),
    ];

    $orders = $this->orderService->getFilteredOrders($filters, 15);
    $statuses = $this->orderService->getAvailableStatuses();
    $stores = $this->orderService->getStoresForFilter();
    $summary = $this->orderService->getOrdersSummary();

    return view('orders.index', compact('orders', 'statuses', 'stores', 'summary', 'filters'));
}
```

**Features:**
- ✅ Pulls filters from query parameters
- ✅ Uses OrderListService for business logic
- ✅ Returns role-filtered data
- ✅ Includes summary statistics
- ✅ Passes filter values back to view (for sticky filters)

---

#### **Updated `update()` Method - Uses Form Request**

```php
public function update(UpdateOrderStatusRequest $request, Order $order)
{
    // Authorization check
    if (!$this->orderService->canEditOrder($order)) {
        abort(403, 'You are not authorized to edit this order');
    }

    $validated = $request->validated();

    // Check status transition
    if (!$this->orderService->canTransitionStatus($order, $validated['status'])) {
        return back()->with('error', 'Invalid status transition');
    }

    // Update with transaction protection
    DB::beginTransaction();
    try {
        $updateData = ['status' => $validated['status']];
        
        if (!empty($validated['assigned_delivery'])) {
            $updateData['assigned_delivery'] = $validated['assigned_delivery'];
        }
        
        $order->update($updateData);
        DB::commit();

        return redirect()->route('orders.show', $order)
                       ->with('success', 'Order updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to update: ' . $e->getMessage());
    }
}
```

---

#### **New `assignDelivery()` Method**

```php
public function assignDelivery(Request $request, Order $order)
{
    // Only admin can assign
    if (!Auth::user()->isAdmin()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'assigned_delivery' => 'required|exists:users,id',
    ]);

    $deliveryPerson = User::findOrFail($validated['assigned_delivery']);

    if (!$deliveryPerson->isDelivery()) {
        return response()->json(['error' => 'Not a delivery person'], 422);
    }

    $order->update([
        'assigned_delivery' => $validated['assigned_delivery'],
        'status' => $order->status === 'Pending' ? 'Approved' : $order->status,
    ]);

    return response()->json([
        'success' => true,
        'message' => "Assigned to {$deliveryPerson->name}",
    ]);
}
```

---

#### **Enhanced `updateStatus()` - AJAX Method**

```php
public function updateStatus(Request $request, Order $order)
{
    $validated = $request->validate([
        'status' => 'required|in:Pending,Approved,Packed,Out for Delivery,Delivered',
    ]);

    // Authorization
    if (!$this->orderService->canAccessOrder($order)) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Status transition validation
    if (!$this->orderService->canTransitionStatus($order, $validated['status'])) {
        return response()->json([
            'error' => "Cannot change from {$order->status} to {$validated['status']}"
        ], 403);
    }

    $order->update(['status' => $validated['status']]);

    return response()->json([
        'success' => true,
        'status' => $order->status,
        'badge_class' => OrderHelper::getStatusBadgeClass($order->status),
        'badge_icon' => OrderHelper::getStatusIcon($order->status),
    ]);
}
```

---

### 6. **Blade View** (`resources/views/sale/order/order_list.blade.php`)

#### **Filter Form**
```blade
<form method="GET" action="{{ route('sale.order.list') }}">
    <!-- Status Filter -->
    <select name="status">
        <option value="all">All Statuses</option>
        @foreach($statuses as $status)
            <option value="{{ $status }}" @selected($filters['status'] === $status)>
                {{ $status }}
            </option>
        @endforeach
    </select>

    <!-- Store Filter (Role-based) -->
    @if($stores->count() > 0)
        <select name="store">
            <option value="all">All Stores</option>
            @foreach($stores as $store)
                <option value="{{ $store->id }}" @selected($filters['store'] == $store->id)>
                    {{ $store->store_name }}
                </option>
            @endforeach
        </select>
    @endif

    <!-- Date Range -->
    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">

    <button type="submit">Filter</button>
    <a href="{{ route('sale.order.list') }}">Reset</a>
</form>
```

#### **Summary Cards**
```blade
<div class="row">
    <div class="col">Total: {{ $summary['total'] }}</div>
    <div class="col">Pending: {{ $summary['pending'] }}</div>
    <div class="col">Delivered: {{ $summary['delivered'] }}</div>
    <div class="col">Revenue: ₹{{ number_format($summary['total_amount']) }}</div>
</div>
```

#### **Order Table**
```blade
<table>
    <thead>
        <tr>
            <th>Order #</th>
            <th>Store</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Assigned To</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->store->store_name }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>₹{{ number_format($order->total_amount) }}</td>
                <td>
                    <span class="badge bg-{{ $badgeClass }}">
                        {{ $order->status }}
                    </span>
                </td>
                <td>{{ $order->createdBy->name }}</td>
                <td>{{ $order->assignedDelivery->name ?? 'Unassigned' }}</td>
                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                <td>
                    <!-- View -->
                    <a href="{{ route('orders.show', $order) }}">View</a>
                    
                    <!-- Edit (Admin) -->
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('orders.edit', $order) }}">Edit</a>
                    @endif
                    
                    <!-- Cancel (Creator/Admin) -->
                    @if(auth()->user()->isAdmin() || $order->created_by === auth()->id())
                        <button onclick="confirmCancel({{ $order->id }})">Cancel</button>
                    @endif
                    
                    <!-- Mark Delivered (Delivery) -->
                    @if(auth()->user()->isDelivery())
                        <button onclick="markDelivered({{ $order->id }})">Delivered</button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No orders found</td>
            </tr>
        @endforelse
    </tbody>
</table>
```

---

### 7. **Status Badge UI**

**CSS Classes:**
```php
public static function getStatusBadgeClass($status): string
{
    return match($status) {
        'Delivered' => 'success',
        'Cancelled' => 'danger',
        'Approved' => 'info',
        'Packed' => 'warning',
        'Out for Delivery' => 'primary',
        default => 'secondary', // Pending
    };
}
```

**Icons Mapping:**
```php
public static function getStatusIcon($status): string
{
    return match($status) {
        'Pending' => 'fas fa-clock',
        'Approved' => 'fas fa-check-circle',
        'Packed' => 'fas fa-box',
        'Out for Delivery' => 'fas fa-truck',
        'Delivered' => 'fas fa-check-double',
        'Cancelled' => 'fas fa-ban',
    };
}
```

---

## 🔐 Role-Based Access Control

### **Admin**
✅ Can see: **ALL orders**  
✅ Can filter by: Status, Store, Date range  
✅ Can edit: Order status, Delivery assignment  
✅ Can cancel: Any order (except delivered/out for delivery)  
✅ Can create: New orders  

### **Sales Person**
✅ Can see: **ONLY orders they created** (`created_by = auth()->id()`)  
✅ Can filter by: Status, Date range  
✅ Can create: New orders  
✅ Can view: Their order details  
✅ Can cancel: Their pending/approved orders  

### **Delivery Person**
✅ Can see: **ONLY assigned orders** (`assigned_delivery = auth()->id()`)  
✅ Can update: Status only to "Delivered"  
✅ Can view: Assigned order details  

---

## 📊 Filter Examples

### Example 1: Get Pending Orders for Store 1
```
GET /orders?status=Pending&store=1
```

### Example 2: Get Orders from Date Range
```
GET /orders?date_from=2026-02-01&date_to=2026-02-28
```

### Example 3: Get Delivered Orders for Current Month
```
GET /orders?status=Delivered&date_from=2026-02&date_to=2026-02-28
```

### Example 4: Get All Filters Combined
```
GET /orders?status=Approved&store=1&date_from=2026-02-01&date_to=2026-02-28
```

---

## 🔄 Status Transitions

### **Valid Transitions:**

```
┌─────────────┐
│   Pending   │ ◄── Order created
└──────┬──────┘
       │ (Admin only)
       ▼
┌──────────────┐
│  Approved    │ ◄── Order approved, can assign delivery
└──────┬───────┘
       │ (Admin only)
       ▼
┌────────────┐
│  Packed    │ ◄── Order is packed
└────────────┘
       │ (Admin only)
       ▼
┌─────────────────────┐
│ Out for Delivery    │ ◄── Assigned to delivery person
└─────────┬───────────┘
          │ (Delivery person)
          ▼
┌──────────────┐
│  Delivered   │ ◄── Order delivered
└──────────────┘
```

### **Role-Based Transitions:**

| From | Admin | Sales | Delivery |
|------|-------|-------|----------|
| Pending | ✅ Approved | ✅ Approved | ❌ |
| Approved | ✅ Packed | ❌ | ❌ |
| Packed | ✅ Out for Delivery | ❌ | ❌ |
| Out for Delivery | ✅ Delivered | ❌ | ✅ Delivered |
| Delivered | ❌ | ❌ | ❌ |

---

## 🧪 Testing Scenarios

### Test 1: Admin Sees All Orders
```
1. Login as admin
2. Visit /orders
3. Verify all orders displayed regardless of creator
```

### Test 2: Sales Sees Only Own Orders
```
1. Login as sales_person_1
2. Visit /orders
3. Verify only orders with created_by = sales_person_1
4. Verify other sales person orders NOT visible
```

### Test 3: Delivery Sees Only Assigned
```
1. Login as delivery_person
2. Visit /orders
3. Verify only assigned orders displayed
4. Verify unassigned orders NOT visible
```

### Test 4: Admin Can Change Status
```
1. Login as admin
2. Go to /orders/{order}/edit
3. Change status from "Pending" to "Approved"
4. Save and verify status updated
5. Verify date updated in database
```

### Test 5: Sales Cannot Edit Order
```
1. Login as sales person
2. Try to access /orders/{order}/edit
3. Verify 403 Forbidden error
```

### Test 6: Delivery Can Only Mark Delivered
```
1. Login as delivery person
2. Visit /orders
3. Click "Mark Delivered" on assigned order
4. Verify AJAX call updates status
5. Verify status changed to "Delivered"
```

### Test 7: Filter by Status Works
```
1. Login as admin
2. Visit /orders?status=Pending
3. Verify only Pending orders shown
4. Visit /orders?status=Delivered
5. Verify only Delivered orders shown
```

### Test 8: Filter by Date Range Works
```
1. Login as admin
2. Visit /orders?date_from=2026-02-01&date_to=2026-02-15
3. Verify only orders from that date range shown
```

### Test 9: Reset Filters Work
```
1. Apply any filters
2. Click "Reset" button
3. Verify all filters cleared
4. Verify all orders displayed
```

### Test 10: Pagination Works
```
1. Create 20+ orders
2. Visit /orders
3. Verify only 15 orders displayed per page
4. Click next page
5. Verify next batch of 15 orders shown
```

---

## 🐛 Troubleshooting

### Issue: Orders not showing for Sales Person
**Solution:** Verify `Order::create()` sets `created_by = Auth::id()`

### Issue: Filters not working
**Solution:** Check query parameters are being passed correctly:
- `status` must match enum values
- `store` must be valid store ID
- `date_from`/`date_to` must be valid date format

### Issue: Status update fails with 403
**Solution:** Verify OrderHelper::canChangeStatus() allows transition for role

### Issue: Delivery person not in dropdown
**Solution:** Verify user has `role = 'delivery'` and `status = 1` (active)

---

## 📝 Summary

| Component | File | Purpose |
|-----------|------|---------|
| Routes | routes/web.php | Define endpoints with role middleware |
| Form Request | UpdateOrderStatusRequest.php | Validate status update input |
| Service | OrderListService.php | Business logic for filtering |
| Model Scopes | Order.php | Query builder shortcuts |
| Controller | OrderController.php | Handle requests, delegate to service |
| View | order_list.blade.php | Display filtered orders, filters, actions |

---

## ✅ Checklist

- [x] Routes with middleware protection
- [x] Role-based query filtering
- [x] Advanced filter form (status, store, date)
- [x] Summary statistics
- [x] Professional Blade UI with badges
- [x] AJAX status update
- [x] Delivery person assignment
- [x] Pagination
- [x] Form request validation
- [x] Service layer for business logic
- [x] Error handling and transactions
- [x] Responsive design

---

**🎉 Your professional Order List Module is complete and ready to use!**
