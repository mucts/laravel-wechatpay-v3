<?php

namespace MuCTS\LaravelWeChatPayV3\Service\CombineTransaction;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['combineTransaction'] = function ($app) {
            return new Client($app);
        };
    }
}
