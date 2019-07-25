<?php

namespace LaravelWechatpayV3\Service\Bill;

use Illuminate\Support\Arr;
use LaravelWechatpayV3\Kernel\BaseClient;
use LaravelWechatpayV3\Kernel\Exceptions\HashException;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public static function className()
    {
        return 'bill';
    }

    /**
     * @param string $id
     * @param string|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function retrieveTradeBill(string $query = null, array $options = [])
    {
        $url = self::classUrl().'tradebill';
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    /**
     * @param string $id
     * @param string|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function retrieveFundFlowBill(string $query = null, array $options = [])
    {
        $url = self::classUrl().'fundflowbill';
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function download($body)
    {
        $response = $this->requestRaw('GET', Arr::get($body, 'download_url'));
        $fileStream = $response->getBody()->getContents();
        $response->getBody()->rewind();

        $hashValue = hash(Arr::get($body, 'hash_type'), $fileStream);
        if ($hashValue != Arr::get($body, 'hash_value')) {
            throw new HashException('账单文件哈希值错误，请尝试重新下载');
        }

        return $fileStream;
    }
}
