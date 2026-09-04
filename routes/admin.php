<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\Loyalty\GiftController as LoyaltyGiftController;
use App\Http\Controllers\Admin\Loyalty\LoyaltyController;
use App\Http\Controllers\Admin\Loyalty\SettingController as LoyaltySettingController;
use App\Http\Controllers\Admin\Marketing\PackController;
use App\Http\Controllers\Admin\Marketing\ProductComplementController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\Stock\MovementController as StockMovementController;
use App\Livewire\Admin\Support\Inbox as SupportInbox;
use App\Livewire\Admin\Support\Thread as SupportThread;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

        Route::resource('brands', BrandController::class)->except(['show']);
        Route::patch('brands/{brand}/toggle', [BrandController::class, 'toggle'])->name('brands.toggle');

        Route::resource('products', ProductController::class)->except(['show']);
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

        Route::get('products/{product}/complements', [ProductComplementController::class, 'index'])->name('products.complements.index');
        Route::post('products/{product}/complements', [ProductComplementController::class, 'store'])->name('products.complements.store');
        Route::delete('products/{product}/complements/{complement}', [ProductComplementController::class, 'destroy'])->name('products.complements.destroy');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
        Route::get('orders/history', [OrderController::class, 'history'])->name('orders.history');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('orders/{order}/invoice', [AdminInvoiceController::class, 'store'])->name('orders.invoice.store');

        Route::get('invoices/{invoice}/telecharger', [AdminInvoiceController::class, 'download'])->name('invoices.download');
        Route::get('invoices/{invoice}/imprimer', [AdminInvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/whatsapp', [AdminInvoiceController::class, 'whatsapp'])->name('invoices.whatsapp');

        Route::resource('articles', ArticleController::class)->except(['show']);
        Route::patch('articles/{article}/toggle', [ArticleController::class, 'toggle'])->name('articles.toggle');

        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', SupportInbox::class)->name('index');
            Route::get('{conversation}', SupportThread::class)->name('show');
        });

        Route::prefix('loyalty')->name('loyalty.')->group(function () {
            Route::get('/', [LoyaltyController::class, 'index'])->name('index');

            Route::put('settings', [LoyaltySettingController::class, 'update'])->name('settings.update');

            Route::get('gifts/create', [LoyaltyGiftController::class, 'create'])->name('gifts.create');
            Route::post('gifts', [LoyaltyGiftController::class, 'store'])->name('gifts.store');
            Route::get('gifts/{gift}/edit', [LoyaltyGiftController::class, 'edit'])->name('gifts.edit');
            Route::put('gifts/{gift}', [LoyaltyGiftController::class, 'update'])->name('gifts.update');
            Route::delete('gifts/{gift}', [LoyaltyGiftController::class, 'destroy'])->name('gifts.destroy');
            Route::patch('gifts/{gift}/toggle', [LoyaltyGiftController::class, 'toggle'])->name('gifts.toggle');
        });

        Route::prefix('marketing')->name('marketing.')->group(function () {
            Route::resource('packs', PackController::class)->except(['show']);
            Route::patch('packs/{pack}/toggle', [PackController::class, 'toggle'])->name('packs.toggle');
        });

        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('movements', [StockMovementController::class, 'index'])->name('movements.index');
            Route::get('movements/create', [StockMovementController::class, 'create'])->name('movements.create');
            Route::post('movements', [StockMovementController::class, 'store'])->name('movements.store');
        });

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::patch('customers/{customer}/notes', [CustomerController::class, 'updateNotes'])->name('customers.notes.update');

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    });
});
