<?php

namespace MuCTS\LaravelWeChatPayV3\Facades;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use MuCTS\LaravelWeChatPayV3\Service\Application;

/**
 * Class WeChatPay
 * @package MuCTS\LaravelWeChatPayV3
 */
class WeChatPay extends Facade
{
    /**
     * @param array $config
     *
     * @return Application
     */
    public static function app(array $config = [])
    {
        return App::make('wechatpay-v3')::app($config);
    }

    protected static function getFacadeAccessor()
    {
        return 'wechatpay-v3';
    }
}