<?php

namespace LaravelWechatpayV3\Kernel\Traits;

use Illuminate\Support\Str;

trait RestfulMethods
{
    /**
     * @param string|array|null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function all($query = null, array $options = [])
    {
        $url = self::classUrl();
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    public static function classUrl()
    {
        return '/v3/'.static::className().'/';
    }

    public static function className()
    {
        $className = get_called_class();
        $classes = explode('\\', $className);
        $classes = array_slice($classes, 2, \count($classes) - 3);
        foreach ($classes as $key => $val) {
            $classes[$key] = $key == count($classes) - 1 ? Str::plural(\strtolower($val)) : \strtolower($val);
        };

        return implode('/', $classes);
    }

    /**
     * @param string $id
     * @param string $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function retrieve(string $id, $query = null, array $options = [])
    {
        $url = $this->instanceUrl($id);
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    public function instanceUrl($id)
    {
        return self::classUrl().$id;
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function create(array $params, array $options = [])
    {
        $url = self::classUrl();
        $opts = $options + ['json' => $params];

        return $this->request('POST', $url, $opts);
    }

    /**
     * @param string $id
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function update(string $id, array $params, array $options = [])
    {
        $url = self::instanceUrl($id);
        $opts = $options + ['json' => $params];

        return $this->request('PUT', $url, $opts);
    }

    /**
     * @param string $id
     * @param string $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function destroy(string $id, string $query, array $options = [])
    {
        $url = self::instanceUrl($id);
        $opts = $options + ['query' => $query];

        return $this->request('DELETE', $url, $opts);
    }
}
