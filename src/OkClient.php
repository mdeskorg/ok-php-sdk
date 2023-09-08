<?php

namespace Mdeskorg\OkPhpSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use http\Exception\RuntimeException;

class OkClient
{
    protected string $appKey;
    protected string $sessionKey;
    protected string $sessionSecretKey;
    protected string $apiUrl = 'https://api.ok.ru/fb.do?';
    private Client $client;

    public function __construct(string $appKey, string $sessionKey, string $sessionSecretKey)
    {
        $this->appKey = $appKey;
        $this->sessionKey = $sessionKey;
        $this->sessionSecretKey = $sessionSecretKey;
        $this->client = new Client();
    }

    private function get($url):array|ClientException
    {
        try {
            $response = $this->client->request('get', $url)->getBody()->getContents();
            return json_decode($response, true);
        } catch (GuzzleException $e) {
            throw new RuntimeException("Request failed: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function getUserInfo(string $uid): array|ClientException
    {
        $params = [
            'fields' => 'first_name,last_name,pic_full',
            'uids' => $uid,
            'method' => 'users.getInfo',
        ];
        $url = $this->getUrlWithSig($params);
        return $this->get($url);
    }

    public function getUrlWithSig(array $params): string
    {
        $param = [
            'application_key' => $this->appKey,
            'format' => 'json',
            'session_key' => $this->sessionKey,
        ];
        $param = array_merge($param, $params);
        $stringParam = '';
        foreach ($param as $key => $value) {
            $stringParam .= $key . '=' . $value;
        }
        $secretKey = md5($stringParam . $this->sessionSecretKey);
        return $this->apiUrl . http_build_query($param) . '&sig=' . $secretKey;

    }

}
