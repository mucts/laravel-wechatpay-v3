<?php

namespace MuCTS\LaravelWeChatPayV3\Service\Ecommerce\Fund\Withdraw;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['withdraw'] = function ($app) {
            return new Client($app);
        };
    }
}
