<?php

namespace MuCTS\Laravel\WeChatPayV3\Service\Ecommerce\Subsidy;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['subsidy'] = function ($app) {
            return new Client($app);
        };
    }
}
