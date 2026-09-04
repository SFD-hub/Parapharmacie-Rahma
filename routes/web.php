<?php

use App\Http\Controllers\Account\AddressController as AccountAddressController;
use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\LoyaltyController;
use App\Http\Controllers\Account\NotificationController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Payment\WebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Livewire\Cart\CartPage;
use App\Livewire\Catalog\ProductCatalog;
use App\Livewire\Support\Chat as SupportChat;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('boutique', ProductCatalog::class)->name('shop.index');
Route::get('produit/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('produit/{product:slug}/avis', [ReviewController::class, 'store'])->name('products.reviews.store');

Route::get('panier', CartPage::class)->name('cart.index');
Route::post('panier/ajouter', [CartController::class, 'store'])->name('cart.store');
Route::patch('panier/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('panier/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('panier', [CartController::class, 'clear'])->name('cart.clear');

Route::get('packs', [PackController::class, 'index'])->name('packs.index');
Route::get('packs/{pack:slug}', [PackController::class, 'show'])->name('packs.show');
Route::post('packs/{pack:slug}/ajouter', [PackController::class, 'addToCart'])->name('packs.addToCart');

// Le chat support est accessible sans compte : les invités sont identifiés
// via leur session (voir SupportConversation::currentGuestToken()).
Route::get('messagerie', SupportChat::class)->name('support.chat');

// Le checkout est accessible sans compte : un invité est identifié via
// Order::currentGuestToken(), au même titre que son panier.
Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('checkout/adresses', [CheckoutController::class, 'storeAddress'])->name('checkout.addresses.store');
Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('checkout/confirmation/{order:order_number}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('conseils-beaute', [ArticleController::class, 'index'])->name('blog.index');
Route::get('conseils-beaute/{article:slug}', [ArticleController::class, 'show'])->name('blog.show');

Route::get('a-propos', [PageController::class, 'show'])->name('pages.about')->defaults('slug', 'a-propos');
Route::get('contact', [PageController::class, 'show'])->name('pages.contact')->defaults('slug', 'contact');
Route::get('faq', [PageController::class, 'show'])->name('pages.faq')->defaults('slug', 'faq');
Route::get('conditions-generales', [PageController::class, 'show'])->name('pages.terms')->defaults('slug', 'conditions-generales');
Route::get('politique-de-confidentialite', [PageController::class, 'show'])->name('pages.privacy')->defaults('slug', 'politique-de-confidentialite');

Route::get('factures/{invoice}/telecharger', [InvoiceController::class, 'download'])
    ->middleware('signed')
    ->name('invoices.download');

Route::prefix('webhooks/payments')->name('webhooks.payments.')->group(function () {
    Route::post('wave', [WebhookController::class, 'wave'])->name('wave');
    Route::post('orange', [WebhookController::class, 'orangeMoney'])->name('orange');
    Route::post('stripe', [WebhookController::class, 'stripe'])->name('stripe');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('mon-compte/adresses', [AccountAddressController::class, 'index'])->name('account.addresses.index');

    Route::get('mon-compte/commandes', [AccountOrderController::class, 'index'])->name('account.orders.index');
    Route::get('mon-compte/commandes/{order:order_number}', [AccountOrderController::class, 'show'])->name('account.orders.show');

    Route::get('mon-compte/fidelite', [LoyaltyController::class, 'index'])->name('account.loyalty.index');
    Route::get('mon-compte/fidelite/historique', [LoyaltyController::class, 'history'])->name('account.loyalty.history');
    Route::post('mon-compte/fidelite/cadeaux/{gift}/echanger', [LoyaltyController::class, 'redeemGift'])->name('account.loyalty.redeemGift');

    Route::get('mon-compte/notifications', [NotificationController::class, 'index'])->name('account.notifications.index');
});

require __DIR__.'/auth.php';
