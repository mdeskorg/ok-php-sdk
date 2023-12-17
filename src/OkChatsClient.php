<?php

namespace Mdeskorg\OkPhpSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use http\Exception\RuntimeException;

class OkChatsClient
{
    private Client $client;
    private string $url = 'https://api.ok.ru/graph/';
    private string $token;

    public function __construct(string $token)
    {
        $this->client = new Client(['headers' => ['Content-Type' => 'application/json;charset=utf-8']]);
        $this->token = $token;
    }

    /**
     * @throws GuzzleException
     */
    private function request($endpoint, $method, $data = null):array|ClientException
    {
        $url = $this->url . $endpoint . '?access_token=' . $this->token;
        try {
            $options = $data ? ['json' => $data] : [];
            $response = $this->client->request($method, $url, $options)->getBody()->getContents();
            return json_decode($response, true);
        } catch (ClientException $e) {
            throw new RuntimeException('Request failed: {$e->getMessage()}', $e->getCode(), $e);
        }
    }


    /**
     * Получение всех чатов группы
     * @throws GuzzleException
     */
    public function getChats(): ClientException|array
    {
        $endpoint = 'me/chats';
        return $this->request($endpoint, 'get');
    }

    /**
     * Проверка валидности токена
     * @throws GuzzleException
     */
    public function validateToken(): ClientException|array
    {
        $endpoint = 'me/info';
        $response = $this->request($endpoint, 'get');

        if (isset($response['error_code'])) {
            throw new Exception('Error Processing Request', 1);
        }
        
        return $response;
    }

    /**
     * Отправка сообщения
     * @throws GuzzleException
     */
    public function sendMessage(string $chatId, string $message = null, array $images = null): array|ClientException
    {
        $endpoint = 'chat:'.$chatId.'/messages';
        if(count($images) > 5){
            return ['error' => 'Max count attachment images - 5'];
        }
        $options = [
            'recipient' => ['chat_id' => 'chat:'.$chatId],
            'message' => []
        ];

        if ($message) {
            $options['message'] = ['text' => $message];
        } else {
            $options['message'] = ['text' => '📎'];
        }

        
        if($images){
            foreach ($images as $key => $value){
                $options['message']['attachments'][] = [
                    'type' => 'IMAGE',
                    'payload' => [
                        'url' => $value
                    ]
                ];
            }
        }
        return $this->request($endpoint, 'post', $options);
    }

    /**
     * Установить вебхук
     * @throws GuzzleException
     */
    public function setWebhook(string $url): array|ClientException
    {
        $endpoint = 'me/subscribe';
        return $this->request($endpoint, 'post', [
            'url' => $url,
            'types' => ['MESSAGE_CREATED','MESSAGE_CALLBACK','CHAT_SYSTEM']
        ]);
    }

    /**
     * Список активных вебхуков
     * @throws GuzzleException
     */
    public function getWebhooks(): array|ClientException
    {
        $endpoint = 'me/subscriptions';
        return $this->request($endpoint, 'get');
    }

    /**
     * Удалить вебхук
     * @throws GuzzleException
     */
    public function deleteWebhook(string $url): array|ClientException
    {
        $endpoint = 'me/unsubscribe';
        return $this->request($endpoint, 'post', [
            'url' => $url,
        ]);
    }


}
