<?php

namespace LaravelWechatpayV3\Service\Ecommerce\Refund;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['refund'] = function ($app) {
            return new Client($app);
        };
    }
}
