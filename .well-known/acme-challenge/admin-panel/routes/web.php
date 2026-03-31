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
use App\Http\Controllers\Delivery\PanelController as DeliveryPanelController;
// sale panel routes
use App\Http\Controllers\Sale\AuthController as SaleAuthController;
use App\Http\Controllers\Sale\SaleController;

/*
|--------------------------------------------------------------------------
| Basic Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('login');
});

Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::get('/logout', [AdminAuthController::class, 'logout']);

Route::get('/dashboard', [DashboardController::class,'index'])
        ->name('dashboard');

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

Route::post('/product/update/{id}', [ProductController::class, 'updateAjax']);

Route::delete('/product/{id}', [ProductController::class, 'destroy'])
    ->name('product.delete');

Route::get('/product/status', [ProductController::class, 'statusPage'])
    ->name('product.status');

Route::post('/product/status/toggle', [ProductController::class, 'toggleStatus'])
    ->name('product.status.toggle');

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

Route::post('/inventory/stock-in', [InventoryController::class, 'stockIn'])
    ->name('inventory.stockIn');
    Route::post('/inventory/stock-out', [InventoryController::class, 'stockOut'])
    ->name('inventory.stockOut');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])
    ->name('inventory.adjust');

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

// convenience route for legacy delivery view
Route::get('/delivery', [DeliveryPersonController::class, 'index'])->name('delivery.index');
// Attendance routes
Route::prefix('attendance')->group(function () {

    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');

    Route::get('/monthly-data', [AttendanceController::class, 'monthlyData'])->name('attendance.monthly');

    Route::get('/auto-data', [AttendanceController::class, 'autoData'])->name('attendance.auto');

    Route::post('/store', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/edit/{id}', [AttendanceController::class, 'edit'])->name('attendance.edit');

    Route::post('/update/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

    Route::delete('/delete/{id}', [AttendanceController::class, 'destroy'])->name('attendance.delete');
});

// Salary routes (protected by authentication)
Route::middleware(['auth'])->prefix('salary')->group(function(){
    Route::get('/', [SalaryController::class,'index'])->name('salary.salary_index');
    Route::get('/list', [SalaryController::class,'listJson'])->name('salary.list');
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

// Store routes
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

// Professional Order Management Routes
// legacy endpoints should be accessible even if the user is not logged in
Route::get('/orders/legacy/list', [OrderController::class, 'list'])
    ->name('order_management.order_index');
Route::get('/orders/legacy/summary', [OrderController::class, 'summary'])
    ->name('orders.summary');

Route::middleware(['auth'])->prefix('orders')->group(function(){
    // Order CRUD with role-based access
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/create', [OrderController::class, 'create'])->name('orders.create')->middleware('role:admin,sales');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store')->middleware('role:admin,sales');
    Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit')->middleware('role:admin');
    Route::put('/{order}', [OrderController::class, 'update'])->name('orders.update')->middleware('role:admin');
    Route::delete('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// Invoices
Route::prefix('invoices')->group(function(){
    Route::get('/list', [App\Http\Controllers\InvoiceController::class,'list'])->name('invoices.list');
    Route::get('/summary', [App\Http\Controllers\InvoiceController::class,'summary'])->name('invoices.summary');
    Route::post('/store', [App\Http\Controllers\InvoiceController::class,'store'])->name('invoices.store');
    Route::post('/update/{id}', [App\Http\Controllers\InvoiceController::class,'update'])->name('invoices.update');
    Route::get('/{id}', [App\Http\Controllers\InvoiceController::class,'show'])->name('invoices.show');
    Route::delete('/delete/{id}', [App\Http\Controllers\InvoiceController::class,'destroy'])->name('invoices.delete');
    Route::get('/{id}/view', [App\Http\Controllers\InvoiceController::class,'view'])->name('invoices.view');
    Route::get('/{id}/download', [App\Http\Controllers\InvoiceController::class,'download'])->name('invoices.download');
});
// reports
Route::prefix('reports')->group(function(){
    Route::get('/', [ReportController::class,'index'])->name('report.report_index');
    Route::get('/list', [ReportController::class,'list'])->name('reports.list');
    Route::get('/summary', [ReportController::class,'summary'])->name('reports.summary');
    Route::get('/chart', [ReportController::class,'chart'])->name('reports.chart');
});
/// admin setting

Route::get('/admin-settings',
    [AdminSettingController::class,'index']
)->name('admin.settings');

Route::post('/admin-settings/update',
    [AdminSettingController::class,'update']
)->name('admin.settings.update');


/*
|--------------------------------------------------------------------------
| Sale Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('sale')->group(function () {

    Route::get('/', [SaleAuthController::class, 'showRegister'])
        ->name('sale.register');
    Route::get('/register', [SaleAuthController::class, 'showRegister'])
        ->name('sale.register.page');
    Route::post('/register', [SaleAuthController::class, 'register'])
        ->name('sale.register.submit');

    Route::get('/login', [SaleAuthController::class, 'showLogin'])
        ->name('sale.login');

    Route::post('/login', [SaleAuthController::class, 'login'])
        ->name('sale.login.submit');

    // OTP endpoints
    Route::post('/send-otp', [SaleAuthController::class, 'sendOtp'])->name('sale.sendOtp');
    Route::post('/verify-otp', [SaleAuthController::class, 'verifyOtp'])->name('sale.verifyOtp');

    // sale dashboard
    Route::get('/dashboard', [SaleController::class, 'index'])->name('sale.dashboard');

    // Additional sale pages
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
    Route::get('/logout', [SaleController::class, 'logout'])->name('sale.logout');

});

/*
|--------------------------------------------------------------------------
| Delivery Panel Routes (Standalone)
|--------------------------------------------------------------------------
*/

Route::prefix('delivery-panel')->group(function () {
    Route::get('/', [DeliveryPanelController::class, 'showRegister'])->name('delivery.panel.home');
    Route::get('/login', [DeliveryPanelController::class, 'showLogin'])->name('delivery.panel.login');
    Route::get('/register', [DeliveryPanelController::class, 'showRegister'])->name('delivery.panel.register');
    Route::get('/otp-verify', [DeliveryPanelController::class, 'showOtpVerify'])->name('delivery.panel.otp.verify.page');
    Route::get('/otp-verify.html', [DeliveryPanelController::class, 'showOtpVerify']);
    Route::post('/login', [DeliveryPanelController::class, 'login'])->name('delivery.panel.login.submit');
    Route::post('/register', [DeliveryPanelController::class, 'register'])->name('delivery.panel.register.submit');
    Route::post('/send-otp', [DeliveryPanelController::class, 'sendOtp'])->name('delivery.panel.sendOtp');
    Route::post('/verify-otp', [DeliveryPanelController::class, 'verifyOtp'])->name('delivery.panel.verifyOtp');
    Route::get('/my-orders', [DeliveryPanelController::class, 'myOrders'])->name('delivery.panel.my.orders');
    Route::get('/order-details/{id?}', [DeliveryPanelController::class, 'orderDetails'])->name('delivery.panel.order.details');

    Route::middleware(['role:delivery'])->group(function () {
        Route::get('/dashboard', [DeliveryPanelController::class, 'dashboard'])->name('delivery.panel.dashboard');
        Route::get('/index', [DeliveryPanelController::class, 'dashboard']);
        Route::get('/attendance', [DeliveryPanelController::class, 'attendancePreview'])->name('delivery.panel.attendance');
        Route::post('/attendance/mark', [DeliveryPanelController::class, 'markAttendance'])->name('delivery.panel.attendance.mark');
        Route::get('/earnings', [DeliveryPanelController::class, 'earningsPreview'])->name('delivery.panel.earnings');
        Route::get('/incentives', [DeliveryPanelController::class, 'earningsPreview'])->name('delivery.panel.incentives');
        Route::get('/profile', [DeliveryPanelController::class, 'profilePreview'])->name('delivery.panel.profile');
        Route::get('/profile/index', [DeliveryPanelController::class, 'profilePreview'])->name('delivery.panel.profile.index');
        Route::post('/profile/update', [DeliveryPanelController::class, 'updateProfile'])->name('delivery.panel.profile.update');
        Route::post('/profile/change-password', [DeliveryPanelController::class, 'changePassword'])->name('delivery.panel.profile.change_password');
        Route::get('/attendnace', function () {
            return redirect()->route('delivery.panel.attendance');
        });
        Route::get('/attendence', function () {
            return redirect()->route('delivery.panel.attendance');
        });
        Route::get('/orders', [DeliveryPanelController::class, 'orders'])->name('delivery.panel.orders');
        Route::get('/order-list', [DeliveryPanelController::class, 'orders']);
        Route::post('/orders/{order}/status', [DeliveryPanelController::class, 'updateOrderStatus'])->name('delivery.panel.orders.status');
        Route::get('/stores', [DeliveryPanelController::class, 'stores'])->name('delivery.panel.stores');
        Route::get('/store-list', [DeliveryPanelController::class, 'stores']);
        Route::get('/overview', [DeliveryPanelController::class, 'profile']);
        Route::post('/logout', [DeliveryPanelController::class, 'logout'])->name('delivery.panel.logout');
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
