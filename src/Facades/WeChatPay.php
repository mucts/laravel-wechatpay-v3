<?php

namespace MuCTS\Laravel\WeChatPayV3\Facades;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use MuCTS\Laravel\WeChatPayV3\Service\Application;

/**
 * Class WeChatPay
 * @package MuCTS\Laravel\WeChatPayV3
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