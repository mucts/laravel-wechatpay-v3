<?php

namespace MuCTS\Laravel\WeChatPayV3\Service\CombineTransaction;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use MuCTS\Laravel\WeChatPayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public static function className()
    {
        return 'combine-transactions';
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function createByApp(array $params, array $options = [])
    {
        return $this->createByChannel('app', $params, $options);
    }

    /**
     * @param string $channel 值仅可为 app 或 jsapi
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function createByChannel(string $channel, array $params, array $options = [])
    {
        $url = self::classUrl().$channel;
        $opts = $options + ['json' => $params];

        return $this->request('POST', $url, $opts);
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
        return $this->createByChannel('jsapi', $params, $options);
    }

    /**
     * @param string $outRefundNo
     * @param string|array|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function retrieveByOutTradeNo(string $outTradeNo, $query = null, array $options = [])
    {
        $url = self::classUrl().'out-trade-no/'.$outTradeNo;
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    /**
     * @param string $outTradeNo
     * @param string|array|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function closeByOutTradeNo(string $outTradeNo, $query = null, array $options = [])
    {
        $url = self::classUrl().'out-trade-no/'.$outTradeNo.'/close';
        $opts = $options + ['query' => $query];

        return $this->request('POST', $url, $opts);
    }

    /**
     * @param $appId
     * @param $timestamp
     * @param $prepayId
     */
    public function generateAppPayInfo($appId, $timestamp, $prepayId, $subMerchantId)
    {
        $payload = [
            'appId' => $appId,
            'timeStamp' => strval($timestamp),
            'nonceStr' => Str::random(32),
            'prepayId' => $prepayId,
        ];
        $payload += [
            'partnerId' => $subMerchantId,
            'packageValue' => 'Sign=WXPay',
            'paySign' => $this->sign($payload),
        ];

        return $payload;
    }

    /**
     * @param $appId
     * @param $timestamp
     * @param $prepayId
     */
    public function generateJsApiPayInfo($appId, $timestamp, $prepayId)
    {
        $payload = [
            'appId' => $appId,
            'timeStamp' => strval($timestamp),
            'nonceStr' => Str::random(32),
            'package' => 'prepay_id='.$prepayId,
        ];
        $payload += [
            'signType' => 'RSA',
            'paySign' => $this->sign($payload),
        ];

        return $payload;
    }
}
