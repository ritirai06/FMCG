# 🚀 Order List Module - Quick Reference

## API Endpoints

### List Orders (Role-Filtered)
```
GET /orders
GET /orders?status=Pending
GET /orders?store=1
GET /orders?date_from=2026-02-01&date_to=2026-02-28
GET /orders?status=Approved&store=1
```

**Response:** Paginated HTML view with filters and summary

**Role Access:**
- Admin: ✅ All orders
- Sales: ✅ Own orders only
- Delivery: ✅ Assigned orders only

---

### Create Order
```
GET /orders/create
POST /orders
```

**Access:** Admin/Sales only (middleware: `role:admin,sales`)

---

### View Order Details
```
GET /orders/{order}
```

**Access:** Admin (all), Sales (own), Delivery (assigned)

---

### Edit Order Status (Form)
```
GET /orders/{order}/edit
```

**Access:** Admin only (middleware: `role:admin`)

---

### Update Order Status (Form Submission)
```
PUT /orders/{order}
```

**Required Fields:**
```json
{
    "status": "Approved",
    "assigned_delivery": 5,
    "notes": "optional notes"
}
```

**Validation:**
- `status`: Required, must be one of: Pending, Approved, Packed, Out for Delivery, Delivered, Cancelled
- `assigned_delivery`: Optional, must exist in users table
- `notes`: Optional, max 1000 characters

**Access:** Admin only

---

### Update Order Status (AJAX)
```
POST /orders/{order}/status
```

**Request Body:**
```json
{
    "status": "Delivered"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Status updated successfully",
    "status": "Delivered",
    "badge_class": "success",
    "badge_icon": "fas fa-check-double"
}
```

**Access:** Authenticated users (role validated internally)

---

### Assign Delivery Person
```
POST /orders/{order}/assign-delivery
```

**Request Body:**
```json
{
    "assigned_delivery": 5
}
```

**Response:**
```json
{
    "success": true,
    "message": "Order assigned to Delivery Person Name",
    "assigned_delivery": "Delivery Person Name"
}
```

**Access:** Admin only

---

### Cancel Order
```
DELETE /orders/{order}/cancel
```

**Permissions:**
- Admin: ✅ Any order
- Sales: ✅ Own orders (not shipped/delivered)
- Delivery: ❌ Cannot cancel

**Side Effects:**
- Stock is automatically restored
- Order status set to "Cancelled"

---

## Service Layer Methods

### OrderListService

```php
// Get filtered orders with pagination
$orders = $this->orderService->getFilteredOrders([
    'status' => 'Pending',
    'store' => 1,
    'date_from' => '2026-02-01',
    'date_to' => '2026-02-28'
], 15);

// Get available statuses
$statuses = $this->orderService->getAvailableStatuses();
// Returns: ['Pending', 'Approved', 'Packed', 'Out for Delivery', 'Delivered', 'Cancelled']

// Get orders summary
$summary = $this->orderService->getOrdersSummary();
// Returns: ['total' => 50, 'pending' => 10, 'delivered' => 30, ...]

// Check role-based access
$canAccess = $this->orderService->canAccessOrder($order);
$canEdit = $this->orderService->canEditOrder($order);
$canCancel = $this->orderService->canCancelOrder($order);

// Validate status transition
$canTransition = $this->orderService->canTransitionStatus($order, 'Approved');

// Get delivery persons
$deliveryPersons = $this->orderService->getDeliveryPersons();

// Get stores for filter
$stores = $this->orderService->getStoresForFilter();
```

---

## Model Query Examples

### Using Scopes

```php
// Get pending orders
Order::pending()->get();

// Get orders from today
Order::today()->get();

// Get delivered orders from this month
Order::thisMonth()->delivered()->get();

// Get orders by date range
Order::byDateRange('2026-02-01', '2026-02-28')->get();

// Get orders by store
Order::byStore(1)->get();

// Get orders with delivery assigned
Order::withDeliveryAssigned()->get();

// Chain multiple scopes
Order::byStatus('Approved')
    ->byStore(1)
    ->byDateRange('2026-02-01', '2026-02-28')
    ->with(['store', 'createdBy', 'assignedDelivery'])
    ->paginate(15);
```

---

## Form Request Validation

### UpdateOrderStatusRequest

```php
// Validation rules
'status' => 'required|in:Pending,Approved,Packed,Out for Delivery,Delivered,Cancelled'
'assigned_delivery' => 'nullable|exists:users,id'
'notes' => 'nullable|string|max:1000'

// Custom messages
'Status is required'
'Invalid status provided'
'Selected delivery person does not exist'
'Notes cannot exceed 1000 characters'

// Usage in controller
public function update(UpdateOrderStatusRequest $request, Order $order)
{
    $validated = $request->validated();
    // All data is validated and clean
}
```

---

## Blade Template Variables

### In `orders.index` view:

```blade
{{-- Pagination object with orders --}}
$orders          // LengthAwarePaginator

{{-- Filter values (for sticky filters) --}}
$filters         // Array: ['status', 'store', 'date_from', 'date_to']

{{-- Available choices --}}
$statuses        // Array of status options
$stores          // Collection of Store models
$deliveryPersons // Collection of User models (delivery role)

{{-- Summary statistics --}}
$summary         // Array: ['total', 'pending', 'delivered', 'total_amount', ...]

{{-- Current user --}}
$user            // Auth::user()
```

---

## Status Badge Classes

```php
OrderHelper::getStatusBadgeClass('Pending')          // 'secondary'
OrderHelper::getStatusBadgeClass('Approved')         // 'info'
OrderHelper::getStatusBadgeClass('Packed')           // 'warning'
OrderHelper::getStatusBadgeClass('Out for Delivery') // 'primary'
OrderHelper::getStatusBadgeClass('Delivered')        // 'success'
OrderHelper::getStatusBadgeClass('Cancelled')        // 'danger'
```

---

## Status Icon Classes

```php
OrderHelper::getStatusIcon('Pending')               // 'fas fa-clock'
OrderHelper::getStatusIcon('Approved')              // 'fas fa-check-circle'
OrderHelper::getStatusIcon('Packed')                // 'fas fa-box'
OrderHelper::getStatusIcon('Out for Delivery')      // 'fas fa-truck'
OrderHelper::getStatusIcon('Delivered')             // 'fas fa-check-double'
OrderHelper::getStatusIcon('Cancelled')             // 'fas fa-ban'
```

---

## JavaScript Functions (In Blade)

### confirmCancel(orderId)
```javascript
confirmCancel(123);  // Shows modal, then POSTs to /orders/123/cancel
```

### markDelivered(orderId)
```javascript
markDelivered(123);  // POSTs to /orders/123/status with status='Delivered'
```

---

## Filter Query Parameters

### Status Filter
```
/orders?status=Pending
/orders?status=Approved
/orders?status=Packed
/orders?status=Out for Delivery
/orders?status=Delivered
/orders?status=Cancelled
/orders?status=all    (shows all - default)
```

### Store Filter
```
/orders?store=1
/orders?store=2
/orders?store=all     (default)
```

### Date Range Filter
```
/orders?date_from=2026-02-01
/orders?date_to=2026-02-28
/orders?date_from=2026-02-01&date_to=2026-02-28
```

### Combined Filters
```
/orders?status=Pending&store=1&date_from=2026-02-01&date_to=2026-02-28
```

---

## Complete Example: Get All Pending Orders for Store 1

### Via Route
```
GET /orders?status=Pending&store=1
```

### Via Service in Controller
```php
$orders = $this->orderService->getFilteredOrders([
    'status' => 'Pending',
    'store' => 1,
], 15);
```

### Via Direct Query
```php
Order::pending()
    ->byStore(1)
    ->with(['store', 'createdBy', 'items.product'])
    ->paginate(15);
```

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── OrderController.php          (Updated)
│   ├── Requests/
│   │   └── UpdateOrderStatusRequest.php (New)
│   └── Middleware/
│       └── CheckRole.php                (Existing)
├── Models/
│   └── Order.php                        (Updated with scopes)
├── Services/
│   └── OrderListService.php             (New)
└── Helpers/
    └── OrderHelper.php                  (Existing)

resources/views/
├── orders/
│   ├── index.blade.php                  (Existing)
│   ├── create.blade.php                 (Existing)
│   ├── show.blade.php                   (Existing)
│   └── edit.blade.php                   (Existing)
└── sale/order/
    └── order_list.blade.php             (Updated)

routes/
└── web.php                              (Already configured)
```

---

## Dependencies

- Laravel 10+ (or your installed version)
- Bootstrap 5 (for UI)
- FontAwesome 6 (for icons)
- User model with roles (admin, sales, delivery)
- Order model with relationships
- OrderHelper class

---

## Common Issues & Solutions

### Issue: Orders not filtering by status
**Check:**
- Status value matches exactly (case-sensitive)
- Status enum in database is correct
- OrderListService::applyStatusFilter() is called

### Issue: Delivery person dropdown empty
**Check:**
- Users exist with role = 'delivery'
- Users have status = 1 (active)
- OrderListService::getDeliveryPersons() returns correct result

### Issue: Status update returns 403
**Check:**
- User role is admin/sales/delivery
- OrderHelper::canChangeStatus() allows transition
- Current status is valid before requested status

### Issue: Filters not persisting (sticky)
**Check:**
- Filter values passed to view in compact()
- Form inputs have value="{{ $filters['status'] ?? '' }}"
- Query parameters preserved in pagination links

### Issue: Role-based filtering not working
**Check:**
- User model has isAdmin(), isSales(), isDelivery() methods
- Role field exists in users table
- OrderListService::applyRoleFilter() validates role

---

## Performance Tips

1. **Use Eager Loading:**
   ```php
   Order::with(['store', 'createdBy', 'assignedDelivery', 'items'])->paginate()
   ```

2. **Use Pagination:**
   ```php
   Order::paginate(15);  // Not get() or all()
   ```

3. **Use Scopes for Common Queries:**
   ```php
   Order::thisMonth()->delivered()->count()  // Cleaner than raw queries
   ```

4. **Index on Foreign Keys:**
   ```sql
   ALTER TABLE orders ADD INDEX idx_created_by (created_by);
   ALTER TABLE orders ADD INDEX idx_assigned_delivery (assigned_delivery);
   ALTER TABLE orders ADD INDEX idx_status (status);
   ```

---

**📚 For complete documentation, see ORDER_LIST_MODULE.md**
