<?php

namespace MuCTS\Laravel\WeChatPayV3;

use MuCTS\Laravel\WeChatPayV3\Service\Application;

class Factory
{
    public static function app(array $config = [])
    {
        return new Application($config);
    }
}