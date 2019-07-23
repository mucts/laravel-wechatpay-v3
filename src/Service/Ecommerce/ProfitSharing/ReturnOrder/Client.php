<?php

namespace LaravelWechatpayV3\Service\Ecommerce\ProfitSharing\ReturnOrder;

use LaravelWechatpayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public function retrieve(string $query = null, array $options = [])
    {
        $url = self::classUrl();
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    public function create(array $params, array $options = [])
    {
        return parent::create($params, $options);
    }
}
