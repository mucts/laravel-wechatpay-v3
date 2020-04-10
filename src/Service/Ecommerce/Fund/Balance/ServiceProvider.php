<?php

namespace MuCTS\LaravelWeChatPayV3\Service\Ecommerce\Fund\Balance;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['balance'] = function ($app) {
            return new Client($app);
        };
    }
}
