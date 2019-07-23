<?php

namespace LaravelWechatpayV3;

use LaravelWechatpayV3\Service\Application;

class Factory
{
    public static function app(array $config = [])
    {
        return new Application($config);
    }
}