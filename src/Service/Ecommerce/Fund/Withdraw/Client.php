<?php

namespace LaravelWechatpayV3\Service\Ecommerce\Fund\Withdraw;

use LaravelWechatpayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    /**
     * @param string $id
     * @param string|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function retrieve(string $id, string $query = null, array $options = [])
    {
        return parent::retrieve($id, $query, $options);
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function create(array $params, array $options = [])
    {
        return parent::create($params, $options);
    }

    public static function className()
    {
        return 'ecommerce/fund/withdraw';
    }
}
