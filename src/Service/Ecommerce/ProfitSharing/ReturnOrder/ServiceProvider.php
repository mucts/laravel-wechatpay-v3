<?php

namespace LaravelWechatpayV3\Service\Ecommerce\ProfitSharing\ReturnOrder;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['profitSharingReturnOrder'] = function ($app) {
            return new Client($app);
        };
    }
}
