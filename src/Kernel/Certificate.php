<?php

namespace LaravelWechatpayV3\Kernel;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use LaravelWechatpayV3\Kernel\Exceptions\DecryptException;
use LaravelWechatpayV3\Kernel\Exceptions\InvalidArgumentException;
use LaravelWechatpayV3\Kernel\Exceptions\RuntimeException;
use LaravelWechatpayV3\Kernel\Exceptions\SignInvalidException;
use LaravelWechatpayV3\Kernel\Utils\AesUtil;
use LaravelWechatpayV3\Service\Certificate\Client;
use Pimple\Container;

class Certificate
{
    const CERTIFICATE_CACHE_PREFIX = 'laravel-wechatpay-v3.kernel.certificate.';
    const SERIAL_NUMBER_CACHE = 'laravel-wechatpay-v3.kernel.serial-no';

    /**
     * @var \Pimple\Container
     */
    protected $app;

    /**
     * Certificate constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @return mixed
     */
    public function getAvailableSerialNo()
    {
        $ttl = Carbon::now()->addHours(12);

        return Cache::remember(self::SERIAL_NUMBER_CACHE, $ttl, function () use ($ttl) {
            /** @var Client $certificateClient */
            $certificateClient = $this->app['certificate'];
            $certificates = collect(Arr::get($certificateClient->all(), 'data'));
            if ($certificates->isEmpty()) {
                throw new SignInvalidException('没有可用的平台证书列表');
            }
            $certificate = $certificates->reduce(function ($carry, $certificate) {
                if (empty($carryExpireTime = Arr::get($carry, 'expire_time'))) {
                    return $certificate;
                }
                $carryExpireTime = Carbon::createFromTimeString($carryExpireTime);
                $expireTime = Carbon::createFromTimeString(Arr::get($certificate, 'expire_time'));

                return $carryExpireTime->gt($expireTime) ? $carryExpireTime : $expireTime;
            });
            if (!$certificate) {
                throw new SignInvalidException('没有可用的平台证书');
            }
            $serialNo = Arr::get($certificate, 'serial_no');
            $aesKey = Config::get('wechatpay-v3.aes_key', '');
            $publicKey = $this->decryptCertificate(Arr::get($certificate, 'encrypt_certificate'), $aesKey);
            Cache::put($this->getPublicKeyCacheKey($serialNo), $publicKey, $ttl);

            return $serialNo;
        });
    }

    /**
     * @param $encryptCertificate
     * @param $aesKey
     * @return bool|string
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws DecryptException
     */
    private function decryptCertificate($encryptCertificate, $aesKey)
    {
        $associatedData = Arr::get($encryptCertificate, 'associated_data');
        $nonceStr = Arr::get($encryptCertificate, 'nonce');
        $cipherText = Arr::get($encryptCertificate, 'ciphertext');
        $publicKey = (new AesUtil($aesKey))->decryptAES256GCM($associatedData, $nonceStr, $cipherText);
        if (!$publicKey) {
            throw new DecryptException('解密证书失败');
        }

        return $publicKey;
    }

    /**
     * @param $serialNo
     * @return string
     */
    private function getPublicKeyCacheKey($serialNo)
    {
        return self::CERTIFICATE_CACHE_PREFIX.$serialNo;
    }

    /**
     * @param $serialNo
     * @return mixed
     * @throws SignInvalidException
     */
    public function getPublicKey($serialNo)
    {
        $ttl = Carbon::now()->addHours(12);

        return Cache::remember($this->getPublicKeyCacheKey($serialNo), $ttl, function () use ($serialNo, $ttl) {
            /** @var Client $certificateClient */
            $certificateClient = $this->app['certificate'];
            $certificates = collect(Arr::get($certificateClient->all(), 'data'));
            $certificate = $certificates->firstWhere('serial_no', '=', $serialNo);
            if (empty($certificate)) {
                throw new SignInvalidException('证书序列号不存在于可用的证书列表中');
            }
            $aesKey = Config::get('wechatpay-v3.aes_key', '');
            $publicKey = $this->decryptCertificate(Arr::get($certificate, 'encrypt_certificate'), $aesKey);
            Cache::put(self::SERIAL_NUMBER_CACHE, $serialNo, $ttl);

            return $publicKey;
        });
    }

}