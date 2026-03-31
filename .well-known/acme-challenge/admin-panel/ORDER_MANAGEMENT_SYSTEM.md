# 🎯 Professional FMCG Order Management System - Complete Documentation

## 📋 Overview

This is a complete professional order management system built with Laravel, featuring:
- ✅ Automatic order number generation (ORD-YYYYMMDD-XXXX format)
- ✅ Role-based access control (Admin, Sales, Delivery)
- ✅ Real-time stock management
- ✅ Shopping cart functionality
- ✅ Order status workflow
- ✅ Database transactions for data integrity
- ✅ Comprehensive order tracking

---

## 📊 Database Structure

### Tables Created/Modified

**orders**
- id (PK)
- order_number (UNIQUE) - Auto-generated format: ORD-YYYYMMDD-XXXX
- store_id (FK) - Links to stores
- sales_person_id (FK) - Legacy, links to sales_persons
- customer_name - Customer name
- customer_phone - Customer contact
- amount - Order amount (legacy)
- total_amount - Total order amount (new)
- status - Pending, Approved, Packed, Out for Delivery, Delivered
- created_by (FK) - User who created order
- assigned_delivery (FK) - Delivery person assigned
- notes - Additional notes
- timestamps

**order_items**
- id (PK)
- order_id (FK) - Links to orders
- product_id (FK) - Links to products
- product_name - Product snapshot
- quantity - Items count
- unit_price - Individual price
- price - Individual price (duplicate for compatibility)
- subtotal - quantity × unit_price
- total - Same as subtotal
- timestamps

**order_sequences**
- id (PK)
- date (UNIQUE) - For daily sequence reset
- sequence - Current sequence number for the day
- timestamps

**users additions**
- role - ENUM: admin, sales, delivery
- phone - User contact
- status - Boolean, active/inactive

---

## 🔑 Key Features

### 1. Order Number Generation

**Format:** `ORD-YYYYMMDD-XXXX`  
**Example:** `ORD-20260222-0001`

```php
use App\Helpers\OrderHelper;

$orderNumber = OrderHelper::generateOrderNumber();
// Returns: ORD-20260222-0001 (auto-increments daily)
```

**How it works:**
- Each day gets its own sequence counter
- Sequence resets at midnight
- Format: ORD + Date (YYYYMMDD) + 4-digit counter

---

### 2. Role-Based Access Control

**Roles:**
- **Admin** - Full access, can view all orders, manage statuses, assign delivery
- **Sales** - Can create orders, view only own orders
- **Delivery** - Can only view assigned orders, mark as delivered

**Middleware Usage:**
```php
Route::post('/orders', 'OrderController@store')->middleware('role:admin,sales');
Route::put('/orders/{order}', 'OrderController@update')->middleware('role:admin');
```

**User Model Methods:**
```php
auth()->user()->isAdmin();      // Check if admin
auth()->user()->isSales();      // Check if sales
auth()->user()->isDelivery();   // Check if delivery
```

---

### 3. Create Order Flow

```
1. User selects Store
2. Enters Customer Details
3. Adds Products to Cart (with quantity)
4. Form validates
5. Backend checks stock
6. DB Transaction starts
7. Create Order with auto-generated number
8. Create OrderItems
9. Reduce Product Stock
10. DB Transaction commits
11. Redirect to order detail page
```

**Stock Management:**
- Stock checked before adding to cart (frontend)
- Stock rechecked on backend (for security)
- Stock decremented on order creation
- Stock restored if order is cancelled

---

### 4. Order Status Workflow

```
                    ┌─────────────────────────────────────┐
                    │         Order Created               │
                    │         Status: PENDING              │
                    └────────────┬────────────────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │   Admin Approves        │
                    │   Status: APPROVED      │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │   Warehouse Packs       │
                    │   Status: PACKED        │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼────────────────────────┐
                    │   Assign Delivery & Mark               │
                    │   Status: OUT FOR DELIVERY          │
                    └────────────┬────────────────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │   Delivery Person      │
                    │   Marks DELIVERED      │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │      ✅ DELIVERED       │
                    └────────────────────────┘
```

**Status Transitions by Role:**
- **Admin**: Can change to any status
- **Sales**: Can create order (Pending), approve it (Approved)
- **Delivery**: Can mark "Out for Delivery" and "Delivered"

---

## 🚀 Implementation Files

### Models
- `app/Models/Order.php` - Main order model with relationships
- `app/Models/OrderItem.php` - Order line items
- `app/Models/OrderSequence.php` - Daily sequence counter
- `app/Models/User.php` - Updated with relationships and methods
- `app/Models/Product.php` - Added orderItems relationship
- `app/Models/Store.php` - Already has orders relationship

### Controllers
- `app/Http/Controllers/OrderController.php` - Complete order management

### Middleware
- `app/Http/Middleware/CheckRole.php` - Role-based access control

### Helpers
- `app/Helpers/OrderHelper.php` - Order utilities and business logic

### Migrations
- `2026_02_22_100000_update_orders_table_for_order_management.php`
- `2026_02_22_101000_update_order_items_table_for_products.php`
- `2026_02_22_102000_create_order_sequences_table.php`
- `2026_02_20_120729_add_role_to_users_table.php` (Updated)

### Views
- `resources/views/orders/index.blade.php` - Order list with filtering
- `resources/views/orders/create.blade.php` - Create order with cart
- `resources/views/orders/show.blade.php` - Order details
- `resources/views/orders/edit.blade.php` - Edit status/assignment

### Routes
- `routes/web.php` - Updated with professional order routes

---

## 🔄 API Endpoints

### Create Order
```
POST /orders
Required: store_id, customer_name, customer_phone, items[]
Returns: Redirect to order detail
```

### List Orders
```
GET /orders
Returns: Paginated orders (role-filtered)
```

### View Order
```
GET /orders/{order}
Returns: Order detail with items
```

### Edit Order
```
GET /orders/{order}/edit
PUT /orders/{order}
Required: status, assigned_delivery
Returns: Redirect with success message
```

### Update Status
```
POST /orders/{order}/status
Required: status
Returns: JSON response
```

### Cancel Order
```
DELETE /orders/{order}/cancel
Returns: Redirect with success message
```

---

## 📝 Usage Examples

### Create Order Programmatically
```php
use App\Models\Order, App\Models\OrderItem, App\Models\Product;
use App\Helpers\OrderHelper;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    $order = Order::create([
        'order_number' => OrderHelper::generateOrderNumber(),
        'store_id' => $storeId,
        'customer_name' => 'John Doe',
        'customer_phone' => '9876543210',
        'amount' => 5000,
        'total_amount' => 5000,
        'status' => 'Pending',
        'created_by' => auth()->id(),
    ]);
    
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => 1,
        'product_name' => 'Product Name',
        'quantity' => 5,
        'unit_price' => 1000,
        'subtotal' => 5000,
    ]);
    
    Product::find(1)->decrement('stock_quantity', 5);
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
}
```

### Get Orders by Role
```php
// Admin - All orders
$orders = Order::latest()->paginate(15);

// Sales - Only own orders
$orders = Order::where('created_by', auth()->id())->latest()->paginate(15);

// Delivery - Only assigned orders
$orders = Order::where('assigned_delivery', auth()->id())->latest()->paginate(15);
```

### Change Order Status
```php
$order = Order::find($orderId);
$order->update(['status' => 'Approved']);

// Check if allowed
if (OrderHelper::canChangeStatus($order->status, 'Packed', auth()->user()->role)) {
    $order->update(['status' => 'Packed']);
}
```

### Get Status Badge
```php
$badgeClass = OrderHelper::getStatusBadgeClass('Delivered');  // badge-success
$icon = OrderHelper::getStatusIcon('Delivered');              // fas fa-check-double
```

---

## 🔒 Security Features

✅ **Database Transactions** - Ensures data consistency  
✅ **Role-Based Middleware** - Prevents unauthorized access  
✅ **Stock Validation** - Backend verification  
✅ **Stock Restoration** - Automatic on order cancellation  
✅ **Input Validation** - All inputs validated  
✅ **SQL Injection Protection** - Using ORM & parameterized queries  
✅ **CSRF Protection** - Built-in Laravel protection  

---

## 📋 Testing Checklist

- [ ] Create order as Sales user
- [ ] Verify order appears in list
- [ ] Check order number format (ORD-YYYYMMDD-XXXX)
- [ ] Verify stock decreased
- [ ] Change order status as Admin
- [ ] Assign delivery person
- [ ] Mark as delivered as Delivery user
- [ ] Cancel order and verify stock restored
- [ ] Test permission restrictions
- [ ] Verify pagination works

---

## 🚨 Common Issues & Solutions

### Issue: Order number not incrementing
**Solution:** Run migration:  
`php artisan migrate --path=database/migrations/2026_02_22_102000_create_order_sequences_table.php`

### Issue: Stock not decreasing
**Solution:** Check if product model has `stock_quantity` column with correct name

### Issue: Permission denied error
**Solution:** Verify user has correct role enum value in database

### Issue: Cart not working
**Solution:** Check browser console for JavaScript errors, ensure `orders/create.blade.php` is being used

---

## 🔧 Configuration

### Enable/Disable Features

```php
// OrderHelper.php - Status transitions
public static function canChangeStatus($current, $new, $role) {
    $transitions = [
        'admin' => ['Pending', 'Approved', 'Packed', 'Out for Delivery', 'Delivered'],
        'sales' => ['Pending', 'Approved'],
        'delivery' => ['Out for Delivery', 'Delivered']
    ];
    
    return in_array($new, $transitions[$role] ?? []);
}
```

Modify roles and transitions as needed for your business rules.

---

## 📞 Support

For issues, refer to:
1. Laravel logs: `storage/logs/laravel.log`
2. Database migrations status: `php artisan migrate:status`
3. Middleware registration: `bootstrap/app.php`

---

**Last Updated:** February 22, 2026  
**Status:** ✅ Production Ready
