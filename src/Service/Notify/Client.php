<?php

namespace LaravelWechatpayV3\Service\Notify;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use LaravelWechatpayV3\Kernel\BaseClient;
use LaravelWechatpayV3\Kernel\Exceptions\SignInvalidException;
use LaravelWechatpayV3\Kernel\Utils\AesUtil;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 */
class Client extends BaseClient
{

    /**
     * @param ResponseInterface $response
     * @return array|mixed
     * @throws SignInvalidException
     */
    public function parseResponse(ResponseInterface $response)
    {
        if (!$this->isResponseSignValid($response)) {
            throw new SignInvalidException('响应验签失败');
        }

        $body = json_decode($response->getBody()->getContents());

        if (JSON_ERROR_NONE !== json_last_error()) {
            return [];
        }

        Arr::set($body, 'data', function () use ($body) {
            $aesKey = Config::get('wechatpay-v3.aes_key', '');
            $associatedData = Arr::get($body, 'resource.associated_data');
            $nonceStr = Arr::get($body, 'resource.nonce');
            $cipherText = Arr::get($body, 'resource.ciphertext');

            return (new AesUtil($aesKey))->decryptAES256GCM($associatedData, $nonceStr, $cipherText);
        });

        return $body;
    }
}
