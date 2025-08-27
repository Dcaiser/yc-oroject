<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Categorycontroller;
use App\Http\Controllers\InventController;
use App\Http\Controllers\activityController;
use App\Http\Controllers\SupplierController;

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


});

// kategori
Route::middleware('auth')->group(function () {
    Route::get('/category', [Categorycontroller::class, 'index'])->name('category');
    Route::get('/addcategory', [Categorycontroller::class, 'create'])->name('addcategory');
    Route::post('/addcategory', [Categorycontroller::class, 'store'])->name('store');
    Route::delete('/deletecategory{id}', [Categorycontroller::class, 'destroy'])->name('deletecategory');


});
//inventory
Route::middleware('auth')->group(function () {
    Route::get('/inventory', [InventController::class, 'index'])->name('invent');
    Route::put('/produk/update-all', [InventController::class, 'update'])->name('updateAll');
    Route::delete('/inventdelete', [InventController::class, 'deleteall'])->name('deleteallinvent');

});

//activity
Route::middleware('auth')->group(function () {

Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::delete('/activities/clear', [ActivityController::class, 'clear'])->name('activities.clear');
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

// Hanya include login dan logout, tanpa register
require __DIR__.'/auth.php';

use App\Http\Controllers\ReportController;

Route::resource('laporan', ReportController::class);

