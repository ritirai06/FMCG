# Commands to Run the Project

## Start the Laravel Development Server

```bash
cd c:\xampp\htdocs\Sales\admin-panel
php artisan serve
```

The server will start at: **http://127.0.0.1:8000**

## Alternative: Use XAMPP

If using XAMPP Apache server:

1. **Start XAMPP:**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

2. **Access the project:**
   - URL: `http://localhost/Sales/admin-panel/public`

## Important URLs

- **Admin Panel:** http://127.0.0.1:8000
- **Login:** http://127.0.0.1:8000/login
- **Dashboard:** http://127.0.0.1:8000/dashboard
- **Sales Person Management:** http://127.0.0.1:8000/sales-person
- **Delivery Person Management:** http://127.0.0.1:8000/delivery-person

## Before Running

Make sure MySQL is running:
```bash
# Check if MySQL is running
net start | findstr MySQL
```

If not running, start it from XAMPP Control Panel.

## Common Issues

**Issue: Port 8000 already in use**
```bash
# Use different port
php artisan serve --port=8001
```

**Issue: Database connection error**
- Check `.env` file has correct database credentials
- Make sure MySQL is running in XAMPP

**Issue: 404 Not Found**
- Make sure you're in the correct directory
- Run: `php artisan route:list` to see all routes

## Quick Start

```bash
# 1. Navigate to project
cd c:\xampp\htdocs\Sales\admin-panel

# 2. Start server
php artisan serve

# 3. Open browser
# Go to: http://127.0.0.1:8000
```

## Stop Server

Press `Ctrl + C` in the terminal where server is running.
