<?php

namespace DoubleThreeDigital\SimpleCommerce;

use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        Fieldtypes\MoneyFieldtype::class,
    ];

    protected $listen = [
        Events\CartCompleted::class => [],
        Events\CartSaved::class => [],
        Events\CartUpdated::class => [],
        Events\CustomerAddedToCart::class => [],
    ];

    protected $routes = [
        'actions' => __DIR__.'/../routes/actions.php',
    ];

    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    protected $tags = [
        Tags\SimpleCommerceTag::class,
    ];

    public function boot()
    {
        parent::boot();

        Statamic::booted(function () {
            $this
                ->bootVendorAssets()
                ->bootRepositories();
        });

        Statamic::afterInstalled(function () {
            (new Content)->setup();
        });

        SimpleCommerce::bootGateways();
    }

    protected function bootVendorAssets()
    {
        $this->publishes([
            __DIR__.'/../config/simple-commerce.php' => config_path('simple-commerce.php'),
        ], 'simple-commerce');

        $this->publishes([
            __DIR__.'/../resources/blueprints' => resource_path('blueprints'),
        ], 'simple-commerce');

        $this->publishes([
            __DIR__.'/../resources/dist' => public_path('vendor/simple-commerce'),
        ], 'simple-commerce');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/simple-commerce'),
        ], 'simple-commerce-translations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/simple-commerce'),
        ], 'simple-commerce-views');

        $this->mergeConfigFrom(__DIR__.'/../config/simple-commerce.php', 'simple-commerce');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'simple-commerce');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'simple-commerce');

        return $this;
    }

    protected function bootRepositories()
    {
        $this->app->bind('Cart', Repositories\CartRepository::class);
        $this->app->bind('Coupon', Repositories\CouponRepository::class);
        $this->app->bind('Currency', Repositories\CurrencyRepository::class);
        $this->app->bind('Customer', Repositories\CustomerRepository::class);

        return $this;
    }
}
