<?php

namespace MuCTS\Laravel\WeChatPayV3\Service\Certificate;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['certificate'] = function ($app) {
            return new Client($app);
        };
    }
}
