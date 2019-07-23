<?php

namespace LaravelWechatpayV3\Service\CombineTransaction;

use LaravelWechatpayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    /**
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function createByApp(array $params, array $options = [])
    {
        return $this->create('app', $params, $options);
    }

    /**
     * @param string $channel 值仅可为 app 或 jsapi
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function create(string $channel, array $params, array $options = [])
    {
        $url = self::classUrl().'/'.$channel;
        $opts = $options + ['json' => $params];

        return $this->request('POST', $url, $opts);
    }

    public static function className()
    {
        return '/combine-transactions';
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function createByJsApi(array $params, array $options = [])
    {
        return $this->create('jsapi', $params, $options);
    }

    /**
     * @param string $outRefundNo
     * @param string|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function retrieveByOutTradeNo(string $outTradeNo, string $query = null, array $options = [])
    {
        $url = self::classUrl().'/out-trade-no/'.$outTradeNo;;
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    /**
     * @param string $outTradeNo
     * @param string|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function closeByOutTradeNo(string $outTradeNo, string $query = null, array $options = [])
    {
        $url = self::classUrl().'/out-trade-no/'.$outTradeNo.'/close';
        $opts = $options + ['query' => $query];

        return $this->request('POST', $url, $opts);
    }
}
