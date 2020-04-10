<?php

namespace MuCTS\LaravelWeChatPayV3;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the provider.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the provider.
     */
    public function register()
    {
        $this->setupConfig();

        $this->app->singleton("wechatpay-v3", function () {
            return new Factory();
        });
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/wechatpay-v3.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('wechatpay-v3.php')], 'wechatpay-v3');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('wechatpay-v3');
        }

        $this->mergeConfigFrom($source, 'wechatpay-v3');
    }
}