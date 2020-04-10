<?php

namespace MuCTS\LaravelWeChatPayV3\Service\Apply4Sub\SubMerchant;

use MuCTS\LaravelWeChatPayV3\Kernel\BaseClient;

/**
 * Class Client.
 *
 */
class Client extends BaseClient
{
    /**
     * @param string $subMerchantId
     * @param null $query
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function retrieveSettlement(string $subMerchantId, $query = null, array $options = [])
    {
        $url = self::classUrl().$subMerchantId.'/settlement';
        $opts = $options + ['query' => $query];

        return $this->request('GET', $url, $opts);
    }

    /**
     * @param string $subMerchantId
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function updateSettlement(string $subMerchantId, array $params, array $options = [])
    {
        $url = self::classUrl().$subMerchantId.'/modify-settlement';
        $opts = $options + ['json' => $params];

        return $this->request('POST', $url, $opts);
    }
}
