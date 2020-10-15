<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
		/*\Braintree_Configuration::environment(env('BTREE_ENVIRONMENT'));
        \Braintree_Configuration::merchantId(env('BTREE_MERCHANT_ID'));
        \Braintree_Configuration::publicKey(env('BTREE_PUBLIC_KEY'));
        \Braintree_Configuration::privateKey(env('BTREE_PRIVATE_KEY'));*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
