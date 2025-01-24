<?php

namespace App\Gateway;

use App\DTO\CreateUserDTO;
use App\Exception\AppException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class ProsvGateway
{
    public function __construct(
        private HttpClientInterface      $client,
        private readonly LoggerInterface $prosvApiLogger,
        private readonly string          $clientId,
        private readonly string          $clientSalt,
        string          $baseUrl,
    )
    {
        $this->client = $client->withOptions([
            'base_uri' => "$baseUrl/api/external/v1/",
        ]);
    }

    public function send($data, $url): array
    {
        $signature = $this->generateSignature($data);

        try {
            $response = $this->client->request(
                'POST',
                "$url/$this->clientId/$signature",
                [
                    'json' => $data
                ]
            );

            $code = $response->getStatusCode();
            $result = $response->toArray(false);

            if ($code != Response::HTTP_OK || $result['error'] != null) {
                throw new TransportException($result['error']['message'] ?? 'ProsvApi::send');
            }
        } catch (Throwable $e) {
            $this->prosvApiLogger->error("ProsvApi::send - $url", [
                'method'  => $url,
                'request' => $data,
                'message' => $e->getMessage(),
            ]);
            return [];
        }

        return $result['result'];
    }

    /**
     * Генерация подписи
     *
     * @param $data - тело запроса
     * @return string
     */
    private function generateSignature($data): string
    {
        return sha1(json_encode($data) . $this->clientSalt);
    }

    /**
     * Получение данных по пользователю на основе его uuid
     * @param string $uuid
     * @return CreateUserDTO|null
     */
    public function getUserDetails(string $uuid): ?CreateUserDTO
    {
        $data = ['actor' => ['uuid' => $uuid], 'attributes' => ['email', 'nameFirst', 'nameLast', 'namePatronymic']];
        $profile = $this->send($data, 'getActorAttributes');

        if (empty($profile)) {
            return null;
        }

        return new CreateUserDTO(
            uuid: $profile['uuid'],
            firstName: $profile['nameFirst'],
            lastName: $profile['nameLast'],
            fatherName: $profile['namePatronymic'],
            email: $profile['email'][0]['value'],
        );
    }
}
