<?php

namespace MuCTS\Laravel\WeChatPayV3\Service\Notify;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use MuCTS\Laravel\WeChatPayV3\Kernel\BaseClient;
use MuCTS\Laravel\WeChatPayV3\Kernel\Utils\AesUtil;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    /**
     * @param array $resource
     * @return array
     * @throws \MuCTS\Laravel\WeChatPayV3\Kernel\Exceptions\InvalidArgumentException
     * @throws \MuCTS\Laravel\WeChatPayV3\Kernel\Exceptions\RuntimeException
     */
    public function parseResource(array $resource)
    {
        $aesKey = Config::get('wechatpay-v3.aes_key', '');
        $associatedData = Arr::get($resource, 'associated_data');
        $nonceStr = Arr::get($resource, 'nonce');
        $cipherText = Arr::get($resource, 'ciphertext');

        $data = (new AesUtil($aesKey))->decryptAES256GCM($associatedData, $nonceStr, $cipherText);

        return \json_decode($data, true);
    }
}
