<?php

namespace MuCTS\LaravelWeChatPayV3\Kernel\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use MuCTS\LaravelWeChatPayV3\Kernel\Certificate;
use MuCTS\LaravelWeChatPayV3\Kernel\Exceptions\SignInvalidException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait SignatureGenerator
{
    protected $authType = 'WECHATPAY2-SHA256-RSA2048';

    /**
     * 生成请求需要的头部签名（放置在请求的头部）
     * @param RequestInterface $request
     * @param array $options
     * @return string
     */
    protected function authHeader(RequestInterface $request, array $options)
    {
        $uri = $request->getUri()->getPath();
        $request->getUri()->getQuery() && $uri .= ('?'.$request->getUri()->getQuery());
        $payload = [
            'method' => strtoupper($request->getMethod()),
            'uri' => $uri,
            'timestamp' => time(),
            'nonce_str' => strtoupper(Str::random(32)),
            'body' => Arr::get($options, 'sign_payload', function () use ($request) {
                $body = $request->getBody()->getContents();
                $request->getBody()->rewind();

                return $body;
            }),
        ];
        $authFormat = '%s mchid="%s",serial_no="%s",nonce_str="%s",timestamp="%s",signature="%s"';

        return sprintf($authFormat, ...[
            $this->authType,
            Config::get('wechatpay-v3.app_id'),
            Config::get('wechatpay-v3.serial_no'),
            $payload['nonce_str'],
            $payload['timestamp'],
            $this->sign($payload),
        ]);
    }

    /**
     * 根据商户私钥生成签名
     *
     * @param array $payload
     * @return string
     */
    public function sign(array $payload)
    {
        $signData = implode("\n", $payload)."\n";
        $clientKey = Config::get('wechatpay-v3.private_key');
        openssl_sign($signData, $sign, $clientKey, OPENSSL_ALGO_SHA256);

        return base64_encode($sign);
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
        if (empty($serialNo)) {
            if (substr(strval($response->getStatusCode()), 0, 1) == '2') {
                throw new SignInvalidException('响应中不存在证书序列号');
            }

            return true;
        }
        $publicKey = (new Certificate($this->app))->getPublicKey($serialNo);

        return boolval(openssl_verify(
            $signData,
            $responseSign,
            $publicKey,
            OPENSSL_ALGO_SHA256
        ));
    }
}
