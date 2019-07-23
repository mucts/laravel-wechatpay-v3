<?php

namespace LaravelWechatpayV3\Kernel\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LaravelWechatpayV3\Kernel\Exceptions\DecryptException;
use LaravelWechatpayV3\Kernel\Exceptions\SignInvalidException;
use LaravelWechatpayV3\Kernel\Utils\AesUtil;
use LaravelWechatpayV3\Service\Certificate\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait SignatureGenerator
{
    protected $authType = 'WECHATPAY2-SHA256-RSA2048';
    private $cachePrefix = 'laravel-wechatpay-v3.kernel.certificate.';

    /**
     * 生成请求需要的签名（放置在请求的头部）
     *
     */
    protected function authHeader(RequestInterface $request, array $options)
    {
        $payload = [
            'method' => strtoupper($request->getMethod()),
            'uri' => '/'.trim($request->getUri()->getPath(), '/'),
            'timestamp' => time(),
            'nonce_str' => strtoupper(Str::random(32)),
            'body' => Arr::get($options, 'sign_payload', function () use ($request) {
                $body = $request->getBody()->getContents();
                $request->getBody()->rewind();

                return $body;
            }),
        ];
        $signData = implode("\n", $payload)."\n";
        $clientKey = Config::get('wechatpay-v3.private_key');

        openssl_sign($signData, $sign, $clientKey, OPENSSL_ALGO_SHA256);
        $sign = base64_encode($sign);
        $authFormat = '%s mchid="%s",serial_no="%s",nonce_str="%s",timestamp="%s",signature="%s"';

        return sprintf($authFormat, ...[
            $this->authType,
            Config::get('wechatpay-v3.app_id'),
            Config::get('wechatpay-v3.serial_no'),
            $payload['nonce_str'],
            $payload['timestamp'],
            $sign,
        ]);
    }

    /**
     * 验证响应的签名
     *
     * @param ResponseInterface $response
     * @return bool
     * @throws SignInvalidException
     */
    protected function isResponseSignValid(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        $payload = [
            'timestamp' => Arr::get($headers, 'Wechatpay-Timestamp.0'),
            'nonce_str' => Arr::get($headers, 'Wechatpay-Nonce.0'),
            'body' => $response->getBody()->getContents(),
        ];
        $response->getBody()->rewind();

        $signData = implode("\n", $payload)."\n";
        $responseSign = base64_decode(Arr::get($headers, 'Wechatpay-Signature.0'));
        $serialNo = Arr::get($headers, 'Wechatpay-Serial.0');

        $publicKey = $this->getPublicKey($serialNo);

        return boolval(openssl_verify(
            $signData,
            $responseSign,
            $publicKey,
            OPENSSL_ALGO_SHA256
        ));
    }

    /**
     * @param $serialNo
     * @return mixed
     * @throws SignInvalidException
     */
    private function getPublicKey($serialNo)
    {
        if (empty($serialNo)) {
            throw new SignInvalidException('响应中的不存在证书序列号');
        }
        $publicKey = Cache::remember(
            $this->getPublicKeyCacheKey($serialNo),
            Carbon::now()->addHours(12),
            function () use ($serialNo) {
                /** @var Client $certificateClient */
                $certificateClient = $this->app['certificate'];
                $certificates = collect(Arr::get($certificateClient->all(), 'data'));
                $certificate = $certificates->firstWhere('serial_no', '=', $serialNo);

                if (empty($certificate)) {
                    throw new SignInvalidException('响应中的证书序列号不存在于可用的证书列表中');
                }
                $aesKey = Config::get('wechatpay-v3.aes_key', '');
                $associatedData = Arr::get($certificate, 'encrypt_certificate.associated_data');
                $nonceStr = Arr::get($certificate, 'encrypt_certificate.nonce');
                $cipherText = Arr::get($certificate, 'encrypt_certificate.ciphertext');
                $publicKey = (new AesUtil($aesKey))->decryptAES256GCM($associatedData, $nonceStr, $cipherText);

                if (!$publicKey) {
                    throw new DecryptException('解密证书失败');
                }

                return $publicKey;
            });

        return $publicKey;
    }

    /**
     * @param $serialNo
     * @return string
     */
    private function getPublicKeyCacheKey($serialNo)
    {
        return $this->cachePrefix.$serialNo;
    }
}
