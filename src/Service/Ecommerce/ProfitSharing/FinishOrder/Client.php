<?php

namespace MuCTS\LaravelWeChatPayV3\Service\Ecommerce\ProfitSharing\FinishOrder;

use MuCTS\LaravelWeChatPayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public function create(array $params, array $options = [])
    {
        return parent::create($params, $options);
    }

    public static function className()
    {
        return 'ecommerce/profitsharing/finish-order';
    }
}
