<?php

namespace LaravelWechatpayV3\Kernel;

use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use LaravelWechatpayV3\Kernel\Exceptions\SignInvalidException;
use LaravelWechatpayV3\Kernel\Traits\Certificate;
use LaravelWechatpayV3\Kernel\Traits\HasHttpRequests;
use LaravelWechatpayV3\Kernel\Traits\ResponseCastable;
use LaravelWechatpayV3\Kernel\Traits\RestfulMethods;
use LaravelWechatpayV3\Kernel\Traits\SignatureGenerator;
use LaravelWechatpayV3\Kernel\Utils\RsaUtil;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BaseClient
{
    use Certificate, RestfulMethods, ResponseCastable, SignatureGenerator, HasHttpRequests {
        request as performRequest;
    }

    protected $baseUri = 'https://api.mch.weixin.qq.com';

    protected $app;

    protected $accessToken;

    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    /**
     * verify the response with certificate
     *
     * @return \Closure
     */
    protected function certificateMiddleware()
    {
        return Middleware::tap(null, function (RequestInterface $request, $options, ResponseInterface $response) {

        });
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function request(string $method, string $url, array $options = [])
    {
        $response = $this->requestRaw($method, $url, $options);

        return $this->castResponse($response);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function requestRaw(string $method, string $url, array $options = [])
    {
        if (empty($this->middlewares)) {
            $this->registerHttpMiddleware();
        }

        return $this->performRequest($url, $method, $options);
    }

    protected function registerHttpMiddleware()
    {
        // retry
        $this->pushMiddleware($this->retryMiddleware(), 'retry');

        // sensitive param
        $this->pushMiddleware($this->sensitiveParamMiddleware(), 'sensitive_param');

        // auth
        $this->pushMiddleware($this->authMiddleware(), 'auth');

        // verify sign
//        $this->pushMiddleware($this->verifySignMiddleware(), 'verify_sign');
    }

    /**
     * Return retry middleware.
     *
     * @return \Closure
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null
        ) {
            if ($retries >= Config::get('http.max_retries', 1)) {
                return false;
            }

            if (is_null($response) || !in_array($response->getStatusCode(), [429, 500, 502, 503])) {
                return false;
            }

            return true;
        }, function () {
            return abs(Config::get('http.retry_delay', 500));
        });
    }

    /**
     * Attache auth to the request header.
     *
     * @return \Closure
     */
    protected function authMiddleware()
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $request = $request->withHeader('Accept', 'application/json');
                $auth = $this->authHeader($request, $options);
                $request = $request->withHeader('Authorization', $auth);

                return $handler($request, $options);
            };
        };
    }

    /**
     * Encrypt/Decrypt sensitive param
     *
     * @return \Closure
     */
    protected function sensitiveParamMiddleware()
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $encodeParams = Arr::get($options, 'encode_params', []);
                if (!empty($encodeParams)) {
                    $body = $request->getBody()->getContents();
                    $request->getBody()->rewind();
                    $params = json_decode($body, true);
                    if (!empty($params)) {
                        $serialNo = $this->getAvailableSerialNo();
                        foreach ($encodeParams as $encodeParam) {
                            $value = Arr::get($params, $encodeParam);
                            if (!is_null($value)) {
                                $encrypted = RsaUtil::publicEncrypt(
                                    $value,
                                    $this->getPublicKey($serialNo)
                                );
                                Arr::set($params, $encodeParam, $encrypted);
                            }
                        }
                        $request = $request->withBody(Psr7\stream_for(json_encode($params)));
                    }
                    $request = $request->withHeader('Wechatpay-Serial', $serialNo);
                }

                /** @var ResponseInterface $response */
                $response = $handler($request, $options);
                $decodeParams = Arr::get($options, 'decode_params', []);
                if (!empty($decodeParams)) {
                    $body = $response->getBody()->getContents();
                    $response->getBody()->rewind();
                    $params = json_decode($body, true);
                    if (!empty($params)) {
                        foreach ($decodeParams as $decodeParam) {
                            $value = Arr::get($params, $decodeParam);
                            if (!is_null($value)) {
                                $decryptedValue = RsaUtil::privateDecrypt(
                                    $value,
                                    Config::get('wechatpay-v3.private_key')
                                );

                                Arr::set($params, $decodeParam, $decryptedValue);
                            }
                        }
                        $response = $response->withBody(Psr7\stream_for(json_encode($params)));
                    }
                }

                return $response;
            };
        };
    }

    /**
     * Attache auth to the request header.
     *
     * @return \Closure
     */
    protected function verifySignMiddleware()
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                /** @var Promise $promise */
                $promise = $handler($request, $options);

                return $promise->then(
                    function (ResponseInterface $response) {
                        if (!$this->isResponseSignValid($response)) {
                            throw new SignInvalidException('响应验签失败');
                        }

                        return $response;
                    }
                );
            };
        };
    }
}