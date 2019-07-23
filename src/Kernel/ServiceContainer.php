<?php

namespace LaravelWechatpayV3\Kernel;

use Pimple\Container;

/**
 * Class ServiceContainer
 */
class ServiceContainer extends Container
{
    /**
     * @var array
     */
    protected $providers = [];

    public function __construct(array $values = [])
    {
        $this->registerProviders($this->getProviders());

        parent::__construct($values);
    }

    /**
     * @param array $providers
     */
    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return array_merge([
            //
        ], $this->providers);
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

}