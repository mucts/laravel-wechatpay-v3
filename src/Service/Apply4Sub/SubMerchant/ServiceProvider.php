<?php

namespace MuCTS\Laravel\WeChatPayV3\Service\Apply4Sub\SubMerchant;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['subMerchant'] = function ($app) {
            return new Client($app);
        };
    }
}
