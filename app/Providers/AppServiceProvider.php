<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\EnsureExclusiveGuardSession;
use App\Listeners\MergeGuestCartOnLogin;
use App\Listeners\MergeGuestSupportConversationOnLogin;
use App\Listeners\NotifyAdminsOfNewOrder;
use App\Models\Category;
use App\Models\SupportConversation;
use App\Services\CartService;
use App\Services\GeneralSettingsService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageManager::class, fn () => ImageManager::usingDriver(Driver::class));
        $this->app->singleton(CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, EnsureExclusiveGuardSession::class);
        Event::listen(Login::class, MergeGuestCartOnLogin::class);
        Event::listen(Login::class, MergeGuestSupportConversationOnLogin::class);
        Event::listen(OrderCreated::class, NotifyAdminsOfNewOrder::class);

        View::composer(['components.web.mobile-menu', 'components.web.header', 'components.web.footer'], function ($view): void {
            $view->with('menuCategories', Cache::remember(
                'nav.categories',
                3600,
                fn () => Category::query()
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('position')
                    ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('position')])
                    ->get(['id', 'parent_id', 'name', 'slug'])
                    ->map(fn (Category $category) => [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'children' => $category->children->map(fn (Category $child) => [
                            'name' => $child->name,
                            'slug' => $child->slug,
                        ])->all(),
                    ])
                    ->all(),
            ));
        });

        View::composer(['components.web.header', 'components.web.mobile-nav'], function ($view): void {
            $view->with('accountRoute', auth()->check() ? route('dashboard') : route('login'));
        });

        View::composer('components.web.mobile-nav', function ($view): void {
            $view->with('cartCount', app(CartService::class)->count());
        });

        View::composer('components.web.header', function ($view): void {
            $view->with('unreadNotificationsCount', auth()->check() ? auth()->user()->notifications()->whereNull('read_at')->count() : 0);
            $view->with('freeShippingThreshold', app(GeneralSettingsService::class)->freeShippingThreshold());
        });

        View::composer('layouts.admin', function ($view): void {
            $view->with('adminUnreadNotificationsCount', auth('admin')->check() ? auth('admin')->user()->notifications()->whereNull('read_at')->count() : 0);
        });

        View::composer('components.web.logo', function ($view): void {
            $settings = app(GeneralSettingsService::class)->all();
            $view->with('logoUrl', $settings['shop_logo']);
            $view->with('shopName', $settings['shop_name']);
        });

        View::composer(['layouts.web', 'layouts.admin'], function ($view): void {
            $view->with('faviconUrl', app(GeneralSettingsService::class)->faviconUrl());
        });

        View::composer('components.web.footer', function ($view): void {
            $view->with('settings', app(GeneralSettingsService::class)->all());
        });

        View::composer('components.web.support-bubble', function ($view): void {
            $conversation = auth()->check()
                ? SupportConversation::query()->where('user_id', auth()->id())->first()
                : (session()->has('support.guest_token')
                    ? SupportConversation::query()->where('guest_token', session('support.guest_token'))->first()
                    : null);

            $view->with('supportUnreadCount', $conversation
                ? $conversation->messages()->whereNotNull('admin_id')->whereNull('read_at')->count()
                : 0);
        });
    }
}
