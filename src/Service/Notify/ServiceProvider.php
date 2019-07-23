<?php

namespace LaravelWechatpayV3\Service\Notify;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['notify'] = function ($app) {
            return new Client($app);
        };
    }
}
