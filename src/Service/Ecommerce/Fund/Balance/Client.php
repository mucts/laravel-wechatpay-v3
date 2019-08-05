<?php

namespace LaravelWechatpayV3\Service\Ecommerce\Fund\Balance;

use LaravelWechatpayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    /**
     * @param string $subMerchantId
     * @param string|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function retrieve(string $subMerchantId, string $query = null, array $options = [])
    {
        return parent::retrieve($subMerchantId, $query, $options);
    }

    public static function className()
    {
        return 'ecommerce/fund/balance';
    }
}
