<?php

namespace MuCTS\LaravelWeChatPayV3\Kernel\Utils;

use MuCTS\LaravelWeChatPayV3\Kernel\Exceptions\InvalidArgumentException;
use MuCTS\LaravelWeChatPayV3\Kernel\Exceptions\RuntimeException;

class AesUtil
{
    const KEY_LENGTH_BYTE = 32;
    const AUTH_TAG_LENGTH_BYTE = 16;

    /**
     * AES key
     *
     * @var string
     */
    private $aesKey;

    /**
     * AesDecrypt constructor.
     * @param $aesKey
     * @throws InvalidArgumentException
     */
    public function __construct($aesKey)
    {
        if (strlen($aesKey) != self::KEY_LENGTH_BYTE) {
            throw new InvalidArgumentException('无效的ApiV3Key，长度应为32个字节');
        }
        $this->aesKey = $aesKey;
    }

    /**
     * @param $associatedData
     * @param $nonceStr
     * @param $cipherText
     * @return bool|string
     * @throws RuntimeException
     */
    public function decryptAES256GCM($associatedData, $nonceStr, $cipherText)
    {
        $cipherText = \base64_decode($cipherText);
        if (strlen($cipherText) <= self::AUTH_TAG_LENGTH_BYTE) {
            return false;
        }

        // ext-sodium (default installed on >= PHP 7.2)
        if (function_exists('\sodium_crypto_aead_aes256gcm_is_available') &&
            \sodium_crypto_aead_aes256gcm_is_available()) {
            return \sodium_crypto_aead_aes256gcm_decrypt($cipherText, $associatedData, $nonceStr, $this->aesKey);
        }

        // ext-libsodium (need install libsodium-php 1.x via pecl)
        if (function_exists('\Sodium\crypto_aead_aes256gcm_is_available') &&
            \Sodium\crypto_aead_aes256gcm_is_available()) {
            return \Sodium\crypto_aead_aes256gcm_decrypt($cipherText, $associatedData, $nonceStr, $this->aesKey);
        }

        // openssl (PHP >= 7.1 support AEAD)
        if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
            $ctext = substr($cipherText, 0, -self::AUTH_TAG_LENGTH_BYTE);
            $authTag = substr($cipherText, -self::AUTH_TAG_LENGTH_BYTE);

            return \openssl_decrypt($ctext, 'aes-256-gcm', $this->aesKey, \OPENSSL_RAW_DATA, $nonceStr,
                $authTag, $associatedData);
        }

        throw new RuntimeException('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
    }
}