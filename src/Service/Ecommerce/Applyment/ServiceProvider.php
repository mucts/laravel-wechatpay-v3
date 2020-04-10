<?php

namespace MuCTS\LaravelWeChatPayV3\Service\Ecommerce\Applyment;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['applyment'] = function ($app) {
            return new Client($app);
        };
    }
}
