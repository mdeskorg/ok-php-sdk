# Odnoklassniki Php sdk
API PHP для "Одноклассники" по работе с группами и пользователями

### Пример получения данных о пользователе.

```php
        $appKey = '***';
        $sessionKey = '***';
        $sessionSecretKey = '***';
        $client = new OkClient($appKey, $sessionKey, $sessionSecretKey);
        $userId = '***'
        return $client->getUserInfo($userId);

//        array:1 [▼
//          0 => array:4 [▼
//              "uid" => "583933080208"
//              "first_name" => "Dymok"
//              "last_name" => "Dymokhodov"
//              "pic_full" => "https://i.mycdn.me/i?r=A0E2egivtM5K0QtANiL-Q08jCdFW-4UxdccMIMHC0dQOSA9xMkabEL003Q-moyPajkhPu_kFbvzHXO2hebzDxCqE"
//              ]
//          ]
        

```

### Пример отправки сообщения.

```php
        $chatId = '***';
        $message = 'Тестовое сообщение';
        $token = '***';
        $images = [
            'https://st.mycdn.me/res/i/ok_logo.png',
            'https://st.mycdn.me/res/i/ok_logo.png'
        ];
        $client = new OkChatsClient($token);
        return $client->sendMessage($chatId, $message, $images);

//      array:3 [▼ 
//          "success" => true
//          "recipient_id" => "chat:C64f8bc880000"
//          "message_id" => "mid:C64f8bc880000.18a753095991615"
//      ]
        

```


