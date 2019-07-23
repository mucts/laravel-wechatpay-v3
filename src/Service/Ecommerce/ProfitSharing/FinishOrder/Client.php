<?php

namespace LaravelWechatpayV3\Service\Ecommerce\ProfitSharing\FinishOrder;

use LaravelWechatpayV3\Kernel\BaseClient;

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
