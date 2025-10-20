<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ============================
// Dashboard
// ============================
Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ============================
// Auth Routes (tidak perlu masuk group auth)
// ============================
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ============================
// Routes untuk user yang login
// ============================
Route::middleware(['auth'])->group(function () {

    // ----- Profile -----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ----- Purchase Orders -----
    // resource dengan parameter custom ($po)
    Route::resource('purchase-orders', PurchaseOrderController::class)
        ->parameters(['purchase-orders' => 'po'])
        ->only(['index', 'create', 'store', 'show', 'update']);

    // hanya marketing yang bisa create
    Route::middleware(['can:create-po'])->group(function () {
        Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::put('/purchase-orders/{po}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
        Route::get('/purchase-orders/{po}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
        Route::delete('/purchase-orders/{po}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
    });

    // custom actions
    Route::post('purchase-orders/{po}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('purchase-orders/{po}/reject', [PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');
    Route::post('purchase-orders/{po}/production', [PurchaseOrderController::class, 'updateProductionStatus'])->name('purchase-orders.production.update')->middleware('can:production-actions');

    // finance update & upload bukti transfer
    Route::post('purchase-orders/{po}/finance-update', [PurchaseOrderController::class, 'updateFinanceStatus'])->name('purchase-orders.finance.update')->middleware('can:finance-actions');

    // SHIPPER update shipping status
    Route::post('purchase-orders/{po}/shipping', [PurchaseOrderController::class, 'updateShippingStatus'])->name('purchase-orders.shipping.update')->middleware('can:shipping-actions'); // gunakan ShippingPolicy

    // routes/web.php
    Route::get('/purchase-orders/{po}/export-pdf', [PurchaseOrderController::class, 'exportPdf'])->name('purchase-orders.export.pdf');

    Route::get('/purchase-orders/{po}/invoice-customer', [PurchaseOrderController::class, 'invoiceCustomer'])->name('purchase-orders.invoice.customer');
    Route::get('/purchase-orders/{po}/customer-order', [PurchaseOrderController::class, 'CustomerOrder'])->name('purchase-orders.customer.order');
    Route::get('/purchase-orders/{po}/order-produksi', [PurchaseOrderController::class, 'OrderProduksi'])->name('purchase-orders.order.produksi');

});

// default auth scaffolding (Breeze/Jetstream)
require __DIR__.'/auth.php';
