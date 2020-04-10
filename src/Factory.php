<?php

namespace MuCTS\LaravelWeChatPayV3;

use MuCTS\LaravelWeChatPayV3\Service\Application;

class Factory
{
    public static function app(array $config = [])
    {
        return new Application($config);
    }
}