# 🚀 Order Management System - Setup & Installation Guide

## ⚡ Quick Start

### 1️⃣ Run Migrations
```bash
# Run all pending migrations (this will add required columns and create new tables)
php artisan migrate

# Or specific migrations only:
php artisan migrate --path=database/migrations/2026_02_22_100000_update_orders_table_for_order_management.php
php artisan migrate --path=database/migrations/2026_02_22_101000_update_order_items_table_for_products.php
php artisan migrate --path=database/migrations/2026_02_22_102000_create_order_sequences_table.php
```

### 2️⃣ Set Up Test Users
Create test users with different roles:

```php
// Run in tinker: php artisan tinker

// Admin User
\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'phone' => '9876543210',
    'role' => 'admin',
    'status' => true
]);

// Sales User
\App\Models\User::create([
    'name' => 'Sales Person',
    'email' => 'sales@example.com',
    'password' => bcrypt('password'),
    'phone' => '9876543211',
    'role' => 'sales',
    'status' => true
]);

// Delivery User
\App\Models\User::create([
    'name' => 'Delivery Person',
    'email' => 'delivery@example.com',
    'password' => bcrypt('password'),
    'phone' => '9876543212',
    'role' => 'delivery',
    'status' => true
]);
```

### 3️⃣ Create Test Data

Create stores and products for testing:

```php
// In tinker:

// Create a store
\App\Models\Store::create([
    'store_name' => 'Test Store',
    'code' => 'TEST001',
    'manager' => 'Store Manager',
    'phone' => '9999999999',
    'address' => '123 Main Street',
    'status' => true
]);

// Create products with stock
\App\Models\Product::create([
    'name' => 'Product A',
    'product_name' => 'Product A',
    'sku' => 'SKU001',
    'sale_price' => 100,
    'price' => 100,
    'stock_quantity' => 100,
    'status' => true
]);

\App\Models\Product::create([
    'name' => 'Product B',
    'product_name' => 'Product B',
    'sku' => 'SKU002',
    'sale_price' => 200,
    'price' => 200,
    'stock_quantity' => 50,
    'status' => true
]);
```

### 4️⃣ Access Routes

#### Orders Dashboard (All Roles)
```
GET /orders
```

#### Create Order (Admin, Sales)
```
GET /orders/create
POST /orders
```

#### Edit Order (Admin Only)
```
GET /orders/{order}/edit
PUT /orders/{order}
```

#### View Order (All Roles)
```
GET /orders/{order}
```

---

## 📊 System Architecture

```
User (Sales/Admin/Delivery)
        ↓
    Authentication (auth()->user())
        ↓
    Route (with middleware 'role')
        ↓
    OrderController
        ├── index() - List orders (role-filtered)
        ├── create() - Show form
        ├── store() - Create order
        ├── show() - View details
        ├── edit() - Edit status
        ├── update() - Update order
        └── cancel() - Cancel order
        ↓
    Models (Order, OrderItem, Product, Store, User)
        ↓
    Database & Transactions
```

---

## 🔍 Troubleshooting

### Problem: Migrations failing
```bash
# Check migration status
php artisan migrate:status

# Roll back if needed
php artisan migrate:rollback
```

### Problem: Role field doesn't exist
```bash
# Check if column was added
php artisan tinker
\App\Models\User::first()->role;

# If missing, run setup manually
php artisan migrate --path=database/migrations/2026_02_20_120729_add_role_to_users_table.php
```

### Problem: Orders table structure incomplete
```bash
# Check columns
\App\Models\Order::first();

# If columns missing, run the order update migration
php artisan migrate --path=database/migrations/2026_02_22_100000_update_orders_table_for_order_management.php
```

### Problem: Order number not generating
```bash
# Check if OrderSequence table was created
php artisan tinker
\Illuminate\Support\Facades\DB::table('order_sequences')->count();

# If empty or table doesn't exist:
php artisan migrate --path=database/migrations/2026_02_22_102000_create_order_sequences_table.php
```

---

## 🧪 Testing Scenarios

### Scenario 1: Create and Track Order
1. Log in as Sales user
2. Go to `/orders/create`
3. Select store, enter customer details
4. Add products to cart
5. Create order
6. See order in list `/orders`
7. Check order appears with auto-generated number

### Scenario 2: Admin Workflow
1. Log in as Admin
2. View all orders at `/orders`
3. Edit order status
4. Assign delivery person
5. Mark as "Out for Delivery"

### Scenario 3: Delivery Workflow
1. Log in as Delivery person
2. View assigned orders at `/orders`
3. Open order detail
4. Mark as "Delivered"

### Scenario 4: Stock Management
1. Check product stock before order
2. Create order with items
3. Verify stock decreased
4. Cancel order
5. Verify stock increased back

---

## 📝 Database Schema

```sql
-- Orders Table
CREATE TABLE orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(255) UNIQUE NOT NULL,
    store_id BIGINT,
    sales_person_id BIGINT,
    customer_name VARCHAR(255),
    customer_phone VARCHAR(20),
    amount DECIMAL(12,2),
    total_amount DECIMAL(12,2),
    status VARCHAR(255) DEFAULT 'Pending',
    created_by BIGINT,
    assigned_delivery BIGINT,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_delivery) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE order_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    product_id BIGINT,
    product_name VARCHAR(255),
    quantity INT DEFAULT 1,
    unit_price DECIMAL(12,2),
    price DECIMAL(12,2),
    subtotal DECIMAL(12,2),
    total DECIMAL(12,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Order Sequences Table
CREATE TABLE order_sequences (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    date DATE UNIQUE,
    sequence INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Users Table Updates
ALTER TABLE users ADD COLUMN role ENUM('admin','sales','delivery') DEFAULT 'sales';
ALTER TABLE users ADD COLUMN phone VARCHAR(20);
ALTER TABLE users ADD COLUMN status BOOLEAN DEFAULT true;
```

---

## 🎯 Features Implemented

✅ Order ID auto-generation (ORD-YYYYMMDD-XXXX)  
✅ Database structure with proper relationships  
✅ Eloquent model relationships  
✅ Role-based middleware  
✅ Create order logic with cart  
✅ Stock management & restoration  
✅ Order status workflow  
✅ DB transactions for data integrity  
✅ Frontend with Bootstrap UI  
✅ JavaScript cart functionality  
✅ Pagination & filtering  
✅ Error handling & validation  

---

## 📚 Files Overview

| File | Purpose |
|------|---------|
| `OrderController.php` | Business logic & actions |
| `Order.php` | Model with relationships |
| `OrderItem.php` | Line item model |
| `OrderSequence.php` | Sequence counter |
| `OrderHelper.php` | Utilities & business rules |
| `CheckRole.php` | Role verification middleware |
| `orders/index.blade.php` | Order list view |
| `orders/create.blade.php` | Create order form |
| `orders/show.blade.php` | Order details |
| `orders/edit.blade.php` | Edit status/assignment |

---

## 🔗 Useful Commands

```bash
# View logs
tail -f storage/logs/laravel.log

# Database console
php artisan tinker

# Create new user
php artisan tinker
> User::create(['name' => '...', 'email' => '...', 'password' => bcrypt('...'), 'role' => 'sales'])

# Reset everything
php artisan migrate:refresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
```

---

## ✨ Next Steps

1. ✅ Run migrations
2. ✅ Create test users
3. ✅ Create test products & stores
4. ✅ Test create order flow
5. ✅ Test role-based access
6. ✅ Test stock management
7. ✅ Customize UI/templates as needed
8. ✅ Add more business rules if needed

---

**Ready to Go! 🚀**

You now have a complete professional order management system!
