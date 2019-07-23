<?php

namespace LaravelWechatpayV3;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade as LaravelFacade;
use LaravelWechatpayV3\Service\Application;

/**
 * Class Facade
 * @package LaravelWechatpayV3
 */
class Facade extends LaravelFacade
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