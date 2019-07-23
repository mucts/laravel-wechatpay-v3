<?php

namespace LaravelWechatpayV3\Service\Ecommerce\ProfitSharing\FinishOrder;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['profitSharingFinishOrder'] = function ($app) {
            return new Client($app);
        };
    }
}
