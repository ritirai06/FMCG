# FMCG Sales & Delivery Management System

A full-stack Laravel 12 web application for managing FMCG (Fast-Moving Consumer Goods) sales operations. It includes a multi-role admin panel, a sales person mobile-style panel, and a delivery partner panel — all in one codebase.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Roles & Access](#roles--access)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Database Setup](#database-setup)
- [Default Login Credentials](#default-login-credentials)
- [Panel URLs](#panel-urls)
- [Key Modules](#key-modules)
- [Deployment (Production)](#deployment-production)
- [Troubleshooting](#troubleshooting)

---

## Features

### Admin Panel
- Dashboard with sales, order, and delivery KPIs
- Product management (CRUD, bulk upload, GST, discount, images)
- Brand, Category, Sub-category management
- Warehouse & Inventory management (Stock In / Stock Out / Adjustment)
- Order management with status workflow and delivery assignment
- Invoice generation and management
- Customer management
- Sales person management (salary, incentive slabs, city/locality assignment)
- Delivery partner management (zone assignment, order assignment)
- Territory management (Cities & Localities)
- Attendance tracking
- Salary & payout management
- Reports & analytics
- Admin settings (company info, logo, preferences)
- User management (Admin / Sales / Delivery roles)

### Sales Panel (`/sale/panel`)
- Mobile-app style UI
- Create and manage orders
- Party (store/customer) management
- Payment collection
- Expense tracking
- Sale returns
- Attendance marking with GPS
- Achievements & incentive view
- Transaction history

### Delivery Panel (`/delivery-panel`)
- OTP-based or email/password login
- Dashboard with assigned order stats
- View and update order status (Assigned → Picked → Out for Delivery → Delivered)
- Order details with timeline
- Store navigation (Google Maps integration)
- Attendance with GPS + photo proof
- Earnings & incentive view
- Profile management

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade templates, Bootstrap 5, Font Awesome |
| Database | MySQL 8+ |
| Charts | ApexCharts |
| Excel Export | PhpSpreadsheet |
| Auth | Laravel Session Auth (multi-role) |
| Queue | Database driver |
| Cache | Database driver |
| Storage | Local disk (public) |

---

## Roles & Access

| Role | Panel URL | Description |
|---|---|---|
| `admin` | `/dashboard` | Full access to all modules |
| `sales` | `/sale/panel/dashboard` | Sales operations only |
| `delivery` | `/delivery-panel/dashboard` | Delivery operations only |

---

## Project Structure

```
admin-panel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Delivery/PanelController.php   # Delivery panel (all routes)
│   │   │   ├── Sale/                          # Sales panel controllers
│   │   │   ├── OrderController.php            # Order CRUD + assignment
│   │   │   ├── InventoryController.php        # Stock In/Out/Adjust
│   │   │   ├── DeliveryPersonController.php   # Delivery partner management
│   │   │   └── ...
│   │   └── Middleware/CheckRole.php           # Role-based access guard
│   ├── Models/
│   │   ├── Order.php                          # assigned_delivery + assigned_delivery_person_id
│   │   ├── DeliveryPerson.php                 # Delivery partner profile
│   │   ├── User.php                           # Auth user (admin/sales/delivery)
│   │   └── ...
│   └── Services/OrderListService.php
├── database/migrations/                       # 60+ migrations
├── resources/views/
│   ├── delivery_panel/                        # Delivery partner UI
│   ├── sale/panel/                            # Sales person UI
│   ├── orders/                                # Admin order views
│   └── ...
├── routes/web.php                             # All web routes
├── public/
│   ├── deliver_assets/                        # Delivery panel static assets
│   └── sale_assets/                           # Sales panel static assets
└── storage/                                   # Uploaded files
```

---

## Requirements

- PHP >= 8.2
- Composer >= 2.x
- MySQL >= 8.0
- Node.js >= 18.x & npm >= 9.x
- XAMPP / Laragon / any local server (for local dev)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-org/fmcg-sales-system.git
cd fmcg-sales-system/admin-panel
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Copy environment file

```bash
cp .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Configure your database in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fmcg_sales
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run migrations

```bash
php artisan migrate
```

### 8. Create storage symlink

```bash
php artisan storage:link
```

### 9. Build frontend assets

```bash
npm run build
```

### 10. Start the development server

```bash
php artisan serve
```

Visit: `http://127.0.0.1:8000`

---

## Environment Setup

Copy `.env.example` to `.env` and fill in the values below.

### Required Variables

```env
# Application
APP_NAME="FMCG Sales System"
APP_ENV=local                        # local | production
APP_KEY=                             # Auto-generated by: php artisan key:generate
APP_DEBUG=true                       # Set false in production
APP_URL=http://127.0.0.1:8000        # Your app URL
APP_TIMEZONE=Asia/Kolkata

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fmcg_sales
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=480                 # Minutes (8 hours)

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Optional Variables

```env
# Mail (for OTP / notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@yourapp.com"
MAIL_FROM_NAME="FMCG Sales"

# Redis (optional, for better performance)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## Database Setup

### Fresh install

```bash
php artisan migrate
```

### Reset and re-migrate (WARNING: deletes all data)

```bash
php artisan migrate:fresh
```

### Seed sample data (if seeders exist)

```bash
php artisan db:seed
```

### Create first admin user manually

After migration, insert an admin user via tinker:

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@example.com',
    'password' => bcrypt('admin123'),
    'role'     => 'admin',
    'status'   => true,
]);
```

### Create a delivery user

```php
App\Models\User::create([
    'name'     => 'Delivery Agent',
    'email'    => 'delivery@example.com',
    'phone'    => '9876543210',
    'password' => bcrypt('delivery123'),
    'role'     => 'delivery',
    'status'   => true,
]);
```

> **Important:** The delivery `User` email/phone **must match** the `DeliveryPerson` record created from the admin panel for order assignment to work correctly.

---

## Default Login Credentials

> These are example credentials. Create your own via tinker (see above).

| Role | URL | Email | Password |
|---|---|---|---|
| Admin | `/login` | admin@example.com | admin123 |
| Sales | `/sale/login` | sales@example.com | sales123 |
| Delivery | `/delivery-panel/login` | delivery@example.com | delivery123 |

---

## Panel URLs

| Panel | URL |
|---|---|
| Admin Login | `/login` |
| Admin Dashboard | `/dashboard` |
| Orders | `/orders` |
| Products | `/products` |
| Inventory | `/inventory` |
| Delivery Partners | `/delivery` |
| Sales Persons | `/sales-person` |
| Reports | `/reports` |
| Sales Login | `/sale/login` |
| Sales Dashboard | `/sale/panel/dashboard` |
| Delivery Login | `/delivery-panel/login` |
| Delivery Dashboard | `/delivery-panel/dashboard` |
| Delivery Debug | `/delivery-panel/debug-ids` *(dev only)* |

---

## Key Modules

### Order Assignment Flow

```
Admin creates order (status: Pending)
    ↓
Admin assigns DeliveryPerson from order detail page
    ↓
System links DeliveryPerson → User account (by email/phone match)
    ↓
Order saved with:
    assigned_delivery_person_id = delivery_persons.id
    assigned_delivery           = users.id
    status                      = Assigned
    ↓
Delivery agent logs in → sees order in dashboard
```

### Inventory Flow

```
Admin selects Warehouse → Product dropdown loads with current stock
    ↓
Stock In  → quantity added to inventory record
Stock Out → quantity deducted (validates sufficient stock)
Adjust    → add or remove with reason logged
    ↓
All actions logged in inventory_logs + audit_logs
```

### Delivery Order Visibility

Orders are shown to a delivery user if **either**:
- `orders.assigned_delivery = users.id` (direct user link), OR
- `orders.assigned_delivery_person_id = delivery_persons.id` (via DeliveryPerson profile match by email or phone)

Use `/delivery-panel/debug-ids` to diagnose if orders are not showing.

---

## Deployment (Production)

### 1. Set environment to production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 3. Build frontend assets

```bash
npm run build
```

### 4. Set correct file permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Set up queue worker (for background jobs)

```bash
php artisan queue:work --daemon
```

Or use Supervisor to keep it running.

### 6. Web server config (Nginx example)

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/fmcg/admin-panel/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Troubleshooting

### Delivery orders not showing in dashboard

1. Visit `/delivery-panel/debug-ids` while logged in as delivery user
2. Check `linked_delivery_person_id` — if `null`, the email/phone on `delivery_persons` table doesn't match the `users` table
3. Fix: ensure the `DeliveryPerson` record has the same email or phone as the `User` account
4. Check `storage/logs/laravel.log` for lines starting with `DeliveryOrders scope:`

### Products not showing in Stock In dropdown

- Select a warehouse first — the product dropdown loads dynamically via AJAX after warehouse selection
- If still empty, check browser console for errors on `/inventory/warehouse/{id}/products`

### 500 / Whoops error on fresh install

```bash
php artisan config:clear
php artisan cache:clear
php artisan migrate
php artisan storage:link
```

### Permission denied on storage

```bash
chmod -R 775 storage bootstrap/cache
```

### Session expired quickly

Increase `SESSION_LIFETIME` in `.env` (value is in minutes):

```env
SESSION_LIFETIME=480
```

---

## License

This project is proprietary software. All rights reserved.
