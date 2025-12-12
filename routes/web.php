<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Categorycontroller;
use App\Http\Controllers\InventController;
use App\Http\Controllers\activityController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ManagerMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

// Dashboard - hanya untuk user yang login
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashController::class, 'index'])->name('dashboard');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User Management - hanya untuk Admin
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show'); // Route yang hilang
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/export', [UserManagementController::class, 'export'])->name('users.export');

    // Bulk delete route
    Route::delete('/users/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('users.bulk-delete');
});
// Product Management - untuk Manager dan Admin
Route::middleware(['auth', ManagerMiddleware::class])->group(function () {
    Route::get('/products', [ProductController::class,'index'])->name('products.index');
    Route::get('/addproducts', [ProductController::class,'create'])->name('products.create');
    Route::post('/addproducts', [ProductController::class,'store'])->name('products.store');
    Route::delete('/deleteproducts{id}', [ProductController::class,'destroy'])->name('products.destroy');
    Route::put('/editproducts/{id}', [ProductController::class,'update'])->name('products.update');
    Route::delete('/deleteproducts', [ProductController::class,'deleteall'])->name('deleteall');
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulkDelete');


});
Route::middleware('auth')->group(function () {
    Route::get('/pos', [Poscontroller::class, 'index'])->name('pos');
    Route::post('/pos/checkout', [Poscontroller::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/status', [Poscontroller::class, 'status'])->name('pos.payments');
    Route::post('/pos/status/{transaction}/pay', [Poscontroller::class, 'applyPayment'])->name('pos.payments.pay');
});


// kategori & satuan
Route::middleware('auth')->group(function () {
    // Categories
    Route::get('/category', [Categorycontroller::class, 'index'])->name('category');
    Route::post('/categories', [Categorycontroller::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [Categorycontroller::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [Categorycontroller::class, 'destroy'])->name('categories.destroy');
    Route::delete('/categories/bulk', [Categorycontroller::class, 'bulkDestroy'])->name('categories.bulk-destroy');

    // Units (satuan)
    Route::post('/units', [UnitsController::class, 'store'])->name('units.store');
    Route::put('/units/{id}', [UnitsController::class, 'update'])->name('units.update');
    Route::delete('/units/{id}', [UnitsController::class, 'destroy'])->name('units.destroy');
    Route::delete('/units/bulk', [UnitsController::class, 'bulkDestroy'])->name('units.bulk-destroy');

    // customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');


});
//inventory
Route::middleware('auth')->group(function () {
    Route::get('/inventory', [InventController::class, 'index'])->name('invent');
    Route::get('/inventory/export', [InventController::class, 'export'])->name('invent.export');
    Route::put('/produk/update-all', [InventController::class, 'update'])->name('updateAll');
    Route::delete('/inventdelete', [InventController::class, 'deleteall'])->name('deleteallinvent');
    Route::get('/inventory/create-stock', [InventController::class, 'createStock'])->name('stock.create');
    Route::post('/inventory/create-stock', [InventController::class, 'updateStock'])->name('addstock');
    Route::get('/inventory/notes', [InventController::class, 'create'])->name('invent_notes');


});

//activity
Route::middleware('auth')->group(function () {

Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::delete('/activities/clear', [ActivityController::class, 'clear'])->name('activities.clear');
Route::delete('/activities/bulk-delete', [ActivityController::class, 'bulkDelete'])->name('activities.bulk-delete');
});

// suppliers
Route::middleware('auth')->group(function () {
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
});



// Reports - untuk Manager dan Admin
Route::middleware(['auth', ManagerMiddleware::class])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-sales', [ReportController::class, 'exportSales'])->name('reports.export-sales');
    Route::get('/reports/export-stock', [ReportController::class, 'exportStock'])->name('reports.export-stock');

    // Generic Export Routes (untuk dashboard reports)
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

    // Supplier Performance Report
    Route::get('/reports/supplier-performance', [ReportController::class, 'supplierPerformance'])->name('reports.supplier-performance');
    Route::get('/reports/supplier-performance/export-pdf', [ReportController::class, 'supplierPerformancePDF'])->name('reports.supplier-performance.pdf');
    Route::get('/reports/supplier-performance/export-excel', [ReportController::class, 'supplierPerformanceExcel'])->name('reports.supplier-performance.excel');
    Route::get('/reports/supplier-performance/export', [ReportController::class, 'supplierPerformanceExport'])->name('reports.supplier-performance.export');

    // Stock Value Report
    Route::get('/reports/stock-value', [ReportController::class, 'stockValue'])->name('reports.stock-value');
    Route::get('/reports/stock-value/export-pdf', [ReportController::class, 'stockValuePDF'])->name('reports.stock-value.pdf');
    Route::get('/reports/stock-value/export-excel', [ReportController::class, 'stockValueExcel'])->name('reports.stock-value.excel');

    // Stock Movement Report
    Route::get('/reports/movement', [ReportController::class, 'stockMovement'])->name('reports.movement');
    Route::get('/reports/movement/export-pdf', [ReportController::class, 'stockMovementPDF'])->name('reports.movement.pdf');
    Route::get('/reports/movement/export-excel', [ReportController::class, 'stockMovementExcel'])->name('reports.movement.excel');
    Route::get('/reports/movement/export', [ReportController::class, 'stockMovementExport'])->name('reports.movement.export');

    // Weekly Report
    Route::get('/reports/weekly', [ReportController::class, 'weeklyReport'])->name('reports.weekly');
    Route::get('/reports/weekly/export-pdf', [ReportController::class, 'weeklyReportPDF'])->name('reports.weekly.pdf');
    Route::get('/reports/weekly/export-excel', [ReportController::class, 'weeklyReportExcel'])->name('reports.weekly.excel');
    Route::get('/reports/weekly/export', [ReportController::class, 'weeklyReportExport'])->name('reports.weekly.export');

    // Monthly Report
    Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/monthly/export-pdf', [ReportController::class, 'monthlyReportPDF'])->name('reports.monthly.pdf');
    Route::get('/reports/monthly/export-excel', [ReportController::class, 'monthlyReportExcel'])->name('reports.monthly.excel');
});

// Hanya include login dan logout, tanpa register
require __DIR__.'/auth.php';


