<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TerritoryController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\SalesPersonController;
use App\Http\Controllers\DeliveryPersonController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Delivery\PanelController as DeliveryPanelController;
// sale panel routes
use App\Http\Controllers\Sale\AuthController as SaleAuthController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Sale\SalesPanelController;

/*
|--------------------------------------------------------------------------
| Basic Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('login');
});

// Static demo: Delivery Panel (Vyapar Bandhu style)
Route::get('/delivery-demo', function () {
    return response()->file(public_path('vyapar-bandhu/index.html'));
});

Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
// Public register disabled — redirect to login
Route::get('/register', fn() => redirect()->route('login'))->name('register');
Route::post('/register', fn() => redirect()->route('login'))->name('register.submit');

Route::get('/dashboard', [DashboardController::class,'index'])
        ->middleware(['auth', 'role:admin'])
        ->name('dashboard');

// ── ADMIN-ONLY ROUTES ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {

// User Management (admin only)
Route::prefix('users')->group(function () {
    Route::get('/',           [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/',          [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('/{id}',       [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/{id}',    [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});

// Customer routes
Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::get('/product/create', [ProductController::class, 'create'])
    ->name('product.create');

Route::post('/product/store', [ProductController::class, 'store'])
    ->name('product.store');

Route::get('/products', [ProductController::class, 'index'])
    ->name('product.index');

Route::get('/product/{id}/fetch', [ProductController::class, 'fetchProduct']);
Route::get('/product/warehouse-stock', [ProductController::class, 'getWarehouseStock'])->name('product.warehouse.stock');

Route::post('/product/update/{id}', [ProductController::class, 'updateAjax']);

Route::delete('/product/{id}', [ProductController::class, 'destroy'])
    ->name('product.delete');

Route::get('/product/status', [ProductController::class, 'statusPage'])
    ->name('product.status');

Route::post('/product/status/toggle', [ProductController::class, 'toggleStatus'])
    ->name('product.status.toggle');

// bulk upload for products
Route::post('/product/bulk', [ProductController::class, 'bulkStore'])
    ->name('product.bulk');

// export products
Route::get('/product/export', [ProductController::class, 'export'])
    ->name('product.export');

/*
|--------------------------------------------------------------------------
| Brand Routes
|--------------------------------------------------------------------------
*/

Route::get('/brands/create', [BrandController::class, 'create'])
    ->name('brands.create');

Route::post('/brands/store', [BrandController::class, 'store'])
    ->name('brands.store');

Route::get('/brands', [BrandController::class, 'index'])
    ->name('brands.index');

Route::get('/brands/{id}/edit', [BrandController::class, 'edit']);

Route::post('/brands/update/{id}', [BrandController::class, 'update']);

Route::delete('/brands/{id}', [BrandController::class, 'destroy']);

Route::get('/brands/status', [BrandController::class, 'statusPage'])
    ->name('brands.status');

Route::post('/brands/toggle-status', [BrandController::class, 'toggleStatus'])
    ->name('brands.toggleStatus');

/*
|--------------------------------------------------------------------------
| Category Routes (Prefix Used Properly)
|--------------------------------------------------------------------------
*/

Route::prefix('categories')->group(function () {

    Route::get('/add', [CategoryController::class,'create'])
        ->name('categories.create');

    Route::post('/store', [CategoryController::class,'store'])
        ->name('categories.store');

    Route::get('/', [CategoryController::class,'index'])
        ->name('categories.index');

    Route::match(['post', 'put'], '/update/{id}', [CategoryController::class,'update'])
        ->name('categories.update');

    Route::delete('/delete/{id}', [CategoryController::class,'destroy'])
        ->name('categories.delete');

    // Category status page (restore)
    Route::get('/status', [CategoryController::class,'statusPage'])
        ->name('categories.status');
});

/*
|--------------------------------------------------------------------------
| SubCategory Routes
|--------------------------------------------------------------------------
*/

Route::prefix('subcategories')->group(function () {

    Route::get('/add', [\App\Http\Controllers\SubCategoryController::class,'create'])
        ->name('subcategories.create');

    Route::post('/store', [\App\Http\Controllers\SubCategoryController::class,'store'])
        ->name('subcategories.store');

    Route::get('/', [\App\Http\Controllers\SubCategoryController::class,'index'])
        ->name('subcategories.index');

    Route::post('/update/{id}', [\App\Http\Controllers\SubCategoryController::class,'update'])
        ->name('subcategories.update');

    Route::delete('/delete/{id}', [\App\Http\Controllers\SubCategoryController::class,'destroy'])
        ->name('subcategories.delete');
});

// API routes for AJAX
Route::prefix('api/subcategories')->group(function () {
    Route::get('/{id}', [\App\Http\Controllers\SubCategoryController::class,'show']);
});

/*
|--------------------------------------------------------------------------
| Warehouse Routes
|--------------------------------------------------------------------------
*/

Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
Route::get('/warehouse/list', [WarehouseController::class, 'index'])->name('warehouse.list');

Route::post('/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
Route::put('/warehouse/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');
Route::delete('/warehouse/{id}', [WarehouseController::class, 'destroy'])->name('warehouse.delete');

Route::post('/warehouse/toggle/{id}', [WarehouseController::class, 'toggle'])->name('warehouse.toggle');
// warehouse status
Route::get('/warehouse/status', [WarehouseController::class, 'status'])
    ->name('warehouse.status');

Route::patch('/warehouse/{id}/toggle-status', [WarehouseController::class, 'toggleStatus'])
    ->name('warehouse.toggleStatus');

    //inventory create
    Route::get('/inventory', [InventoryController::class,'index'])
        ->name('inventory.index');
    Route::get('/inventory.html', function() { return redirect('/inventory'); });

Route::post('/inventory/stock-in', [InventoryController::class, 'stockIn'])
    ->name('inventory.stockIn');
    Route::post('/inventory/stock-out', [InventoryController::class, 'stockOut'])
    ->name('inventory.stockOut');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])
    ->name('inventory.adjust');

// New inventory management routes
Route::post('/inventory/add-quantity', [InventoryController::class, 'addQuantity'])
    ->name('inventory.addQuantity');
Route::post('/inventory/remove-quantity', [InventoryController::class, 'removeQuantity'])
    ->name('inventory.removeQuantity');
Route::get('/inventory/warehouse-stock/{productId}', [InventoryController::class, 'getWarehouseStock'])
    ->name('inventory.warehouseStock');
Route::get('/inventory/logs/{productId}', [InventoryController::class, 'getInventoryLogs'])
    ->name('inventory.logs');
Route::get('/inventory/transactions', [InventoryController::class, 'getTransactions'])
    ->name('inventory.transactions');

    // ajax helper for warehouse-specific product listing
    Route::get('/inventory/warehouse/{id}/products', [InventoryController::class, 'warehouseProducts'])
        ->name('inventory.warehouseProducts');
   // Territory / City & Locality
Route::prefix('territory')->group(function () {

    Route::get('/', [TerritoryController::class,'index'])
        ->name('cities.index');

    Route::post('/city/store', [TerritoryController::class,'storeCity'])
        ->name('city.store');

    Route::post('/locality/store', [TerritoryController::class,'storeLocality'])
        ->name('locality.store');

    Route::post('/city/delete', [TerritoryController::class, 'deleteCity'])
        ->name('city.delete');

    Route::post('/locality/delete', [TerritoryController::class, 'deleteLocality'])
        ->name('locality.delete');

        Route::get('/get-localities/{city}', [TerritoryController::class,'getLocalities']);
Route::post('/assign-localities', [TerritoryController::class,'assignLocalities'])
        ->name('assign.localities');
       Route::get('/get-localities/{city}',
    [TerritoryController::class,'getLocalities']
)->name('get.localities');

});

// Sales Person routes
Route::prefix('sales-person')->group(function () {
    Route::get('/', [SalesPersonController::class, 'index'])->name('sales.person.index');
    Route::post('/store', [SalesPersonController::class, 'store'])->name('sales.person.store');
    Route::post('/delete', [SalesPersonController::class, 'destroy'])->name('sales.person.delete');
    Route::post('/toggle-status', [SalesPersonController::class, 'toggleStatus'])->name('sales.person.toggleStatus');
    Route::get('/{id}/details', [SalesPersonController::class, 'details'])->name('sales.person.details');
    Route::post('/assign-cities', [SalesPersonController::class, 'assignCities'])->name('sales.person.assignCities');
    Route::post('/assign-localities', [SalesPersonController::class, 'assignLocalitiesForSales'])->name('sales.person.assignLocalities');
    Route::post('/update-salary', [SalesPersonController::class, 'updateSalary'])->name('sales.person.updateSalary');
    Route::post('/update-incentive', [SalesPersonController::class, 'updateIncentive'])->name('sales.person.updateIncentive');
    
    // Geolocation routes
    Route::post('/update-location', [SalesPersonController::class, 'updateLocation'])->name('sales.person.updateLocation');
    Route::get('/{id}/location-history', [SalesPersonController::class, 'locationHistory'])->name('sales.person.locationHistory');
    Route::post('/toggle-location-tracking', [SalesPersonController::class, 'toggleLocationTracking'])->name('sales.person.toggleLocationTracking');
});

// Delivery Person routes
Route::prefix('delivery-person')->group(function () {
    Route::get('/', [DeliveryPersonController::class, 'index'])->name('delivery.person.index');
    Route::post('/store', [DeliveryPersonController::class, 'store'])->name('delivery.person.store');
    Route::post('/update', [DeliveryPersonController::class, 'update'])->name('delivery.person.update');
    Route::post('/delete', [DeliveryPersonController::class, 'destroy'])->name('delivery.person.delete');
    Route::get('/{id}/details', [DeliveryPersonController::class, 'details'])->name('delivery.person.details');
    Route::post('/assign-zones', [DeliveryPersonController::class, 'assignZones'])->name('delivery.person.assignZones');
    Route::post('/assign-orders', [DeliveryPersonController::class, 'assignOrders'])->name('delivery.person.assignOrders');
    Route::post('/toggle-status', [DeliveryPersonController::class, 'toggleStatus'])->name('delivery.person.toggleStatus');
});

// convenience route for legacy delivery view - direct view without authentication
Route::get('/delivery', [DeliveryPersonController::class, 'index'])->name('delivery.index');
Route::get('/delivery_partners.html', function() { return redirect('/delivery'); });
Route::get('/delivery-partners', function() { return redirect('/delivery'); });
// Attendance routes
Route::prefix('attendance')->group(function () {

    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');

    Route::get('/monthly-data', [AttendanceController::class, 'monthlyData'])->name('attendance.monthly');

    Route::get('/auto-data', [AttendanceController::class, 'autoData'])->name('attendance.auto');

    Route::post('/store', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::post('/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    Route::get('/edit/{id}', [AttendanceController::class, 'edit'])->name('attendance.edit');

    Route::post('/update/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

    Route::delete('/delete/{id}', [AttendanceController::class, 'destroy'])->name('attendance.delete');
});

// Salary routes (admin only)
Route::prefix('salary')->group(function(){
    Route::get('/', [SalaryController::class,'index'])->name('salary.salary_index');
    Route::get('/list', [SalaryController::class,'listJson'])->name('salary.list');
    Route::get('/{id}', [SalaryController::class,'show'])->name('salary.show');
    Route::post('/store', [SalaryController::class,'store'])->name('salary.store');
    Route::get('/edit/{id}', [SalaryController::class,'edit'])->name('salary.edit');
    Route::post('/update/{id}', [SalaryController::class,'update'])->name('salary.update');
    Route::delete('/delete/{id}', [SalaryController::class,'destroy'])->name('salary.delete');
    Route::post('/monthly-summary', [SalaryController::class,'monthlySummary'])->name('salary.monthly');
    // Payout endpoints
    Route::post('/payouts/store', [App\Http\Controllers\SalaryPayoutController::class,'store'])->name('salary.payouts.store');
    Route::get('/payouts/list', [App\Http\Controllers\SalaryPayoutController::class,'list'])->name('salary.payouts.list');
    // Incentive slab CRUD
    Route::get('/slabs/list', [App\Http\Controllers\IncentiveSlabController::class,'list'])->name('slabs.list');
    Route::post('/slabs/store', [App\Http\Controllers\IncentiveSlabController::class,'store'])->name('slabs.store');
    Route::post('/slabs/update/{id}', [App\Http\Controllers\IncentiveSlabController::class,'update'])->name('slabs.update');
    Route::delete('/slabs/delete/{id}', [App\Http\Controllers\IncentiveSlabController::class,'destroy'])->name('slabs.delete');
});

// Redirect routes for legacy URLs
Route::get('/salary_management.html', function() { return redirect('/salary'); });
Route::get('/salary-management', function() { return redirect('/salary'); });

// Universal .html fallback - catches any URL ending with .html and redirects to version without .html
Route::get('/{path}.html', function($path) {
    return redirect('/' . $path);
})->where('path', '.*');

}); // end admin middleware group

// Store routes (admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
Route::prefix('store')->group(function(){
    Route::get('/', [StoreController::class,'index'])->name('store.store_index');
    Route::get('/list', [StoreController::class,'list'])->name('store.list');
    Route::post('/store', [StoreController::class,'store'])->name('store.store');
    Route::get('/edit/{id}', [StoreController::class,'edit'])->name('store.edit');
    Route::post('/update/{id}', [StoreController::class,'update'])->name('store.update');
    Route::delete('/delete/{id}', [StoreController::class,'destroy'])->name('store.delete');

    // Inventory
    Route::get('/inventory/list', [App\Http\Controllers\StoreInventoryController::class,'list'])->name('store.inventory.list');
    Route::post('/inventory/store', [App\Http\Controllers\StoreInventoryController::class,'store'])->name('store.inventory.store');
    Route::get('/inventory/edit/{id}', [App\Http\Controllers\StoreInventoryController::class,'edit'])->name('store.inventory.edit');
    Route::post('/inventory/update/{id}', [App\Http\Controllers\StoreInventoryController::class,'update'])->name('store.inventory.update');
    Route::delete('/inventory/delete/{id}', [App\Http\Controllers\StoreInventoryController::class,'destroy'])->name('store.inventory.delete');

    // Contacts
    Route::get('/contacts/list', [App\Http\Controllers\StoreContactController::class,'list'])->name('store.contacts.list');
    Route::post('/contacts/store', [App\Http\Controllers\StoreContactController::class,'store'])->name('store.contacts.store');
    Route::get('/contacts/edit/{id}', [App\Http\Controllers\StoreContactController::class,'edit'])->name('store.contacts.edit');
    Route::post('/contacts/update/{id}', [App\Http\Controllers\StoreContactController::class,'update'])->name('store.contacts.update');
    Route::delete('/contacts/delete/{id}', [App\Http\Controllers\StoreContactController::class,'destroy'])->name('store.contacts.delete');

    // Settings
    Route::get('/settings/list', [App\Http\Controllers\StoreSettingController::class,'list'])->name('store.settings.list');
    Route::post('/settings/store', [App\Http\Controllers\StoreSettingController::class,'store'])->name('store.settings.store');
    Route::get('/settings/edit/{id}', [App\Http\Controllers\StoreSettingController::class,'edit'])->name('store.settings.edit');
    Route::post('/settings/update/{id}', [App\Http\Controllers\StoreSettingController::class,'update'])->name('store.settings.update');
    Route::delete('/settings/delete/{id}', [App\Http\Controllers\StoreSettingController::class,'destroy'])->name('store.settings.delete');
});
}); // end store admin group

// Legacy order endpoints — auth required
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/orders/legacy/list', [OrderController::class, 'list'])->name('order_management.order_index');
    Route::get('/orders/legacy/summary', [OrderController::class, 'summary'])->name('orders.summary');
});

// Order routes — admin only for index, role-scoped per action
Route::middleware(['auth', 'role:admin'])->prefix('orders')->group(function(){
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/insert-sample', [OrderController::class, 'showInsertSample'])->name('orders.insert-sample-form');
    Route::post('/insert-sample', [OrderController::class, 'insertSample'])->name('orders.insert-sample');
    Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/api/delivery-agents', [OrderController::class, 'deliveryAgentsByLocality'])->name('orders.deliveryAgents');
    Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/{order}/assign-delivery', [OrderController::class, 'assignDeliveryAgent'])->name('orders.assignDelivery');
});



// ── Invoices: Web (role-based) ────────────────────────────────────────────
Route::middleware('auth')->prefix('invoices')->group(function () {
    Route::get('/',                [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.web.index');
    Route::get('/create',          [App\Http\Controllers\InvoiceController::class, 'create'])->name('invoices.web.create')->middleware('role:admin,sales');
    Route::post('/',               [App\Http\Controllers\InvoiceController::class, 'storeWeb'])->name('invoices.web.store')->middleware('role:admin,sales');
    Route::get('/{id}',            [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.web.show');
    Route::post('/{id}/status',    [App\Http\Controllers\InvoiceController::class, 'updateStatus'])->name('invoices.web.status');
    Route::post('/{id}/assign',    [App\Http\Controllers\InvoiceController::class, 'assignDelivery'])->name('invoices.web.assign')->middleware('role:admin');
    Route::get('/{id}/view',       [App\Http\Controllers\InvoiceController::class, 'view'])->name('invoices.view');
    Route::get('/{id}/download',   [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');
    Route::delete('/{id}',         [App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.delete')->middleware('role:admin');
    // Legacy JSON
    Route::get('/list',            [App\Http\Controllers\InvoiceController::class, 'list'])->name('invoices.list');
    Route::get('/summary',         [App\Http\Controllers\InvoiceController::class, 'summary'])->name('invoices.summary');
    Route::post('/update/{id}',    [App\Http\Controllers\InvoiceController::class, 'update'])->name('invoices.update');
});

// ── Invoices: API (Sanctum token auth) ────────────────────────────────────
Route::prefix('api')->group(function () {
    Route::post('/auth/login',                    [App\Http\Controllers\InvoiceController::class, 'apiLogin']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/invoices',                   [App\Http\Controllers\InvoiceController::class, 'apiIndex']);
        Route::post('/invoice',                   [App\Http\Controllers\InvoiceController::class, 'apiStore']);
        Route::get('/invoice/{id}',               [App\Http\Controllers\InvoiceController::class, 'apiShow']);
        Route::put('/invoice/{id}/status',        [App\Http\Controllers\InvoiceController::class, 'apiUpdateStatus']);
        Route::put('/invoice/{id}/assign-delivery', [App\Http\Controllers\InvoiceController::class, 'apiAssignDelivery']);
    });
});
// Reports (admin only)
Route::middleware(['auth', 'role:admin'])->prefix('reports')->group(function(){
    Route::get('/', [ReportController::class,'index'])->name('report.report_index');
    Route::get('/list', [ReportController::class,'list'])->name('reports.list');
    Route::get('/summary', [ReportController::class,'summary'])->name('reports.summary');
    Route::get('/chart', [ReportController::class,'chart'])->name('reports.chart');
    Route::get('/analytics', [ReportController::class,'analytics'])->name('reports.analytics');
    Route::get('/filter/brands', [ReportController::class,'filterBrands'])->name('reports.filter.brands');
    Route::get('/filter/products', [ReportController::class,'filterProducts'])->name('reports.filter.products');
    Route::get('/margin', [ReportController::class,'margin'])->name('reports.margin');
    Route::get('/margin/export', [ReportController::class,'marginExport'])->name('reports.margin.export');
});

// Redirect for reports_analytics
Route::get('/reports_analytics', function() { return redirect('/reports'); });
Route::get('/order_management', function() { return redirect('/orders'); });

// Admin settings (admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin-settings', [AdminSettingController::class,'index'])->name('admin.settings');
    Route::post('/admin-settings/update', [AdminSettingController::class,'update'])->name('admin.settings.update');
    Route::delete('/admin-settings/{id}', [AdminSettingController::class,'destroy'])->name('admin.settings.destroy');
});


/*
|--------------------------------------------------------------------------
| Sale Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('sale')->group(function () {

    // ── Sales Auth (dedicated login) ─────────────────────────────────
    Route::get('/',          fn() => redirect()->route('sale.login'))->name('sale.register');
    Route::get('/register',  [SaleAuthController::class, 'showRegister'])->name('sale.register.page');
    Route::post('/register', [SaleAuthController::class, 'register'])->name('sale.register.submit');
    Route::get('/login',     [SaleAuthController::class, 'showLogin'])->name('sale.login');
    Route::post('/login',    [SaleAuthController::class, 'login'])->name('sale.login.submit');
    Route::post('/send-otp',   [SaleAuthController::class, 'sendOtp'])->name('sale.sendOtp');
    Route::post('/verify-otp', [SaleAuthController::class, 'verifyOtp'])->name('sale.verifyOtp');

    // sale dashboard — now redirects to new mobile-app panel
    Route::get('/dashboard', function () {
        return redirect()->route('sale.panel.dashboard');
    })->middleware('auth')->name('sale.dashboard');

    // Legacy sale pages — protected
    Route::middleware(['auth', 'role:sales,admin'])->group(function () {
        Route::get('/orders', [SaleController::class, 'orders'])->name('sale.orders');
        Route::get('/order/list', [SaleController::class, 'orders'])->name('sale.order.list');
        Route::get('/order/history', [SaleController::class, 'orders'])->name('sale.order.history');
        Route::get('/order/order_list', function() { return redirect()->route('sale.order.list'); });
        Route::get('/order/create', [SaleController::class, 'createOrder'])->name('sale.order.create');
        Route::post('/order/store', [SaleController::class, 'storeOrder'])->name('sale.order.store');
        Route::get('/stores', [SaleController::class, 'stores'])->name('sale.stores');
        Route::get('/store/list', [SaleController::class, 'stores'])->name('sale.store.list');
        Route::get('/store/create', [SaleController::class, 'createStore'])->name('sale.store.create');
        Route::post('/store/store', [SaleController::class, 'storeStore'])->name('sale.store.store');
        Route::get('/attendance', [SaleController::class, 'attendance'])->name('sale.attendance');
        Route::post('/attendance/mark', [SaleController::class, 'markAttendance'])->name('sale.attendance.mark');
        Route::get('/profile', [SaleController::class, 'profile'])->name('sale.profile');
        Route::post('/profile/update', [SaleController::class, 'updateProfile'])->name('sale.profile.update');
    });
    Route::get('/logout', [SaleController::class, 'logout'])->name('sale.logout');

});

/*
|--------------------------------------------------------------------------
| Sales Panel (Mobile-App Style) Routes
|--------------------------------------------------------------------------
*/
Route::prefix('sale/panel')->middleware(['auth', 'role:sales,admin'])->group(function () {
    Route::get('/dashboard',    [SalesPanelController::class, 'dashboard'])->name('sale.panel.dashboard');

    // Parties
    Route::get('/parties',              [SalesPanelController::class, 'parties'])->name('sale.panel.parties');
    Route::get('/parties/create',       [SalesPanelController::class, 'partyCreate'])->name('sale.panel.party.create');
    Route::post('/parties',             [SalesPanelController::class, 'partyStore'])->name('sale.panel.party.store');
    Route::get('/parties/{id}',         [SalesPanelController::class, 'partyShow'])->name('sale.panel.party.show');
    Route::get('/parties/{id}/edit',    [SalesPanelController::class, 'partyEdit'])->name('sale.panel.party.edit');
    Route::put('/parties/{id}',         [SalesPanelController::class, 'partyUpdate'])->name('sale.panel.party.update');

    // Items
    Route::get('/items',                [SalesPanelController::class, 'items'])->name('sale.panel.items');
    Route::post('/items',               [SalesPanelController::class, 'storeItem'])->name('sale.panel.items.store');

    // New Sale
    Route::get('/sale/new',             [SalesPanelController::class, 'newSale'])->name('sale.panel.sale.new');
    Route::post('/sale/store',          [SalesPanelController::class, 'storeSale'])->name('sale.panel.sale.store');

    // Payment In
    Route::get('/payment/in',           [SalesPanelController::class, 'paymentIn'])->name('sale.panel.payment.in');
    Route::post('/payment/store',       [SalesPanelController::class, 'storePayment'])->name('sale.panel.payment.store');

    // Transactions
    Route::get('/transactions',         [SalesPanelController::class, 'transactions'])->name('sale.panel.transactions');

    // Returns
    Route::get('/returns',              [SalesPanelController::class, 'returns'])->name('sale.panel.returns');
    Route::post('/returns/store',       [SalesPanelController::class, 'storeReturn'])->name('sale.panel.return.store');

    // Attendance
    Route::get('/attendance',           [SalesPanelController::class, 'attendance'])->name('sale.panel.attendance');
    Route::post('/attendance/mark',     [SalesPanelController::class, 'markAttendance'])->name('sale.panel.attendance.mark');

    // Expenses
    Route::get('/expenses',             [SalesPanelController::class, 'expenses'])->name('sale.panel.expenses');
    Route::post('/expenses/store',      [SalesPanelController::class, 'storeExpense'])->name('sale.panel.expense.store');

    // Achievements
    Route::get('/achievements',         [SalesPanelController::class, 'achievements'])->name('sale.panel.achievements');

    // API
    Route::get('/api/products',         [SalesPanelController::class, 'apiProducts'])->name('sale.panel.api.products');
    Route::get('/api/customer/{id}/orders', [SalesPanelController::class, 'apiCustomerOrders'])->name('sale.panel.api.customer.orders');
});

/*
|--------------------------------------------------------------------------
| Delivery Panel Routes (Standalone)
|--------------------------------------------------------------------------
*/

Route::prefix('delivery-panel')->group(function () {
    // ── Delivery Auth (dedicated login) ────────────────────────────
    Route::get('/',          fn() => redirect()->route('delivery.panel.login'))->name('delivery.panel.home');
    Route::get('/login',     [DeliveryPanelController::class, 'showLogin'])->name('delivery.panel.login');
    Route::post('/login',    [DeliveryPanelController::class, 'login'])->name('delivery.panel.login.submit');
    Route::get('/register',  [DeliveryPanelController::class, 'showRegister'])->name('delivery.panel.register');
    Route::post('/register', [DeliveryPanelController::class, 'register'])->name('delivery.panel.register.submit');
    Route::get('/otp-verify',fn() => redirect()->route('delivery.panel.login'))->name('delivery.panel.otp.verify.page');
    Route::get('/otp-verify.html', fn() => redirect()->route('delivery.panel.login'));
    Route::post('/send-otp',   [DeliveryPanelController::class, 'sendOtp'])->name('delivery.panel.sendOtp');
    Route::post('/verify-otp', [DeliveryPanelController::class, 'verifyOtp'])->name('delivery.panel.verifyOtp');
    // ── Debug route: visit /delivery-panel/debug-ids to diagnose assignment issues ──
    Route::get('/debug-ids', function () {
        if (!auth()->check() || auth()->user()->role !== 'delivery') {
            return response()->json(['error' => 'Login as delivery user first'], 403);
        }
        $user = auth()->user();
        $dp = \App\Models\DeliveryPerson::where('email', $user->email)->first();
        if (!$dp) {
            $phone = $user->phone ?? $user->mobile ?? null;
            if ($phone) $dp = \App\Models\DeliveryPerson::where('phone', $phone)->first();
        }
        $orders = \App\Models\Order::where(function ($q) use ($user, $dp) {
            $q->where('assigned_delivery', $user->id);
            if ($dp) $q->orWhere('assigned_delivery_person_id', $dp->id);
        })->get(['id', 'order_number', 'status', 'assigned_delivery', 'assigned_delivery_person_id']);
        return response()->json([
            'logged_in_user_id'    => $user->id,
            'user_email'           => $user->email,
            'user_phone'           => $user->phone ?? $user->mobile ?? null,
            'linked_delivery_person_id' => $dp?->id,
            'linked_delivery_person_name' => $dp?->name,
            'orders_found'         => $orders->count(),
            'orders'               => $orders,
        ]);
    })->name('delivery.panel.debug');

    Route::middleware(['auth', 'role:delivery'])->group(function () {
        Route::get('/my-orders', [DeliveryPanelController::class, 'orders'])->name('delivery.panel.my.orders');
        Route::get('/order-details/{id?}', [DeliveryPanelController::class, 'orderDetails'])->name('delivery.panel.order.details');
        Route::get('/dashboard', [DeliveryPanelController::class, 'dashboard'])->name('delivery.panel.dashboard');
        Route::get('/index', [DeliveryPanelController::class, 'dashboard']);
        Route::get('/attendance', [DeliveryPanelController::class, 'attendancePreview'])->name('delivery.panel.attendance');
        Route::post('/attendance/mark', [DeliveryPanelController::class, 'markAttendance'])->name('delivery.panel.attendance.mark');
        Route::get('/items', [DeliveryPanelController::class, 'items'])->name('delivery.panel.items');
        Route::get('/earnings', [DeliveryPanelController::class, 'earningsPreview'])->name('delivery.panel.earnings');
        Route::get('/incentives', [DeliveryPanelController::class, 'earningsPreview'])->name('delivery.panel.incentives');
        Route::get('/profile', [DeliveryPanelController::class, 'profilePreview'])->name('delivery.panel.profile');
        Route::get('/profile/index', [DeliveryPanelController::class, 'profilePreview'])->name('delivery.panel.profile.index');
        Route::post('/profile/update', [DeliveryPanelController::class, 'updateProfile'])->name('delivery.panel.profile.update');
        Route::post('/profile/change-password', [DeliveryPanelController::class, 'changePassword'])->name('delivery.panel.profile.change_password');
        Route::get('/orders', [DeliveryPanelController::class, 'orders'])->name('delivery.panel.orders');
        Route::get('/order-list', [DeliveryPanelController::class, 'orders']);
        Route::post('/orders/{order}/status', [DeliveryPanelController::class, 'updateOrderStatus'])->name('delivery.panel.orders.status');
        Route::get('/stores', [DeliveryPanelController::class, 'stores'])->name('delivery.panel.stores');
        Route::get('/store-list', [DeliveryPanelController::class, 'stores']);
        Route::get('/overview', [DeliveryPanelController::class, 'profile']);
        Route::post('/logout', [DeliveryPanelController::class, 'logout'])->name('delivery.panel.logout');
        // Transactions (invoices assigned to delivery user)
        Route::get('/transactions', [DeliveryPanelController::class, 'transactions'])->name('delivery.panel.transactions');
        Route::get('/transactions/{id}', [DeliveryPanelController::class, 'transactionDetail'])->name('delivery.panel.transaction.show');
        Route::post('/transactions/{id}/status', [DeliveryPanelController::class, 'transactionUpdateStatus'])->name('delivery.panel.transaction.status');
    });
});

// URL alias support for underscore path
Route::prefix('delivery_panel')->group(function () {
    Route::get('/', function () {
        return redirect('/delivery-panel');
    });
    Route::get('/{any}', function ($any) {
        return redirect('/delivery-panel/' . $any);
    })->where('any', '.*');
});
