<?php

namespace App\Service;

class Hmac
{
    /**
     * Алгоритм хеширования, используемый для HMAC
     */
    protected string $algorithm;

    public function __construct(string $algorithm = 'sha256')
    {
        $this->algorithm = $algorithm;
    }

    /**
     * Подписывает сообщение с использованием HMAC и секретного ключа
     *
     * @param string $message Сообщение, которое нужно подписать
     * @param string $secretKey Секретный ключ, используемый для подписи
     * @return string Подпись в формате base64url
     */
    public function sign(string $message, string $secretKey): string
    {
        $digest = hash_hmac($this->algorithm, $message, $secretKey, true);

        return rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
    }

    /**
     * Хеширует сообщение с использованием выбранного алгоритма
     *
     * @param string $message Сообщение для хэширования
     * @return string Хеш в формате base64
     */
    public function hash(string $message): string
    {
        return base64_encode(hash($this->algorithm, $message, true));
    }
}
