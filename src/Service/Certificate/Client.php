<?php

namespace LaravelWechatpayV3\Service\Certificate;

use LaravelWechatpayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public function all(string $query = null, array $options = [])
    {
        return parent::all($query, $options);
    }

    protected function registerHttpMiddleware()
    {
        // auth
        $this->pushMiddleware($this->authMiddleware(), 'auth');

        // retry
        $this->pushMiddleware($this->retryMiddleware(), 'retry');

    }
}
