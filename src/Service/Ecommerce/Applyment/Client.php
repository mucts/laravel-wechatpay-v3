<?php

namespace MuCTS\LaravelWeChatPayV3\Service\Ecommerce\Applyment;

use MuCTS\LaravelWeChatPayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public function retrieve(string $id, $query = null, array $options = [])
    {
        return parent::retrieve($id, $query, $options);
    }

    public function create(array $params, array $options = [])
    {
        return parent::create($params, $options);
    }
}
