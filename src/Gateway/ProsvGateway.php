<?php

namespace App\Gateway;

use App\DTO\CreateUserResponse;
use App\DTO\ProsvPropertyResponse;
use App\Enum\ProsvAttribute;
use App\Exception\ProsvException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class ProsvGateway
{
    public function __construct(
        private HttpClientInterface      $client,
        private readonly LoggerInterface $prosvApiLogger,
        private readonly string          $clientId,
        private readonly string          $clientSalt,
        string                           $baseUrl,
        private NormalizerInterface $serializer
    )
    {
        $this->client = $client->withOptions(['base_uri' => "$baseUrl/api/external/v1/"]);
    }

    /**
     * @param array  $data
     * @param string $url
     *
     * @return array
     */
    public function send(array $data, string $url): array
    {
        $signature = $this->generateSignature($data);

        try {
            $response = $this->client->request('POST', "$url/$this->clientId/$signature", ['json' => $data]);

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
     * @param array $data Тело запроса
     * @return string
     */
    private function generateSignature(array $data): string
    {
        return sha1(json_encode($data) . $this->clientSalt);
    }

    /**
     * Получение данных по пользователю на основе его uuid
     *
     * @param string $uuid
     * @return CreateUserResponse|null
     */
    public function getUserDetails(string $uuid): ?CreateUserResponse
    {
        $data = ['actor' => ['uuid' => $uuid], 'attributes' => ['email', 'nameFirst', 'nameLast', 'namePatronymic']];
        $profile = $this->send($data, 'getActorAttributes');

        if (empty($profile)) {
            return null;
        }

        return new CreateUserResponse(
            uuid: $profile['uuid'],
            firstName: $profile['nameFirst'],
            lastName: $profile['nameLast'],
            fatherName: $profile['namePatronymic'],
            email: $profile['email'][0]['value'],
        );
    }

    /**
     * Получение справочника по атрибуту
     * request body:  {"attribute":"series","limit":100,"page":2}
     * @param ProsvAttribute $attribute - атрибут справочника
     * @return mixed
     * @throws ProsvException
     */
    public function getAttributes(ProsvAttribute $attribute): mixed
    {
        $data = ['attribute' => $attribute, 'keys' => ['uuid', 'name']];

        if ($attribute == ProsvAttribute::District) {
            $data["keys"][] = "code";
        }

        $response = $this->send($data, 'getReference');

        if (!isset($response['records'])) {
            throw new ProsvException('ProsvGateway::getAttributes empty records');
        }

        return $this->serializer->denormalize($response['records'], ProsvPropertyResponse::class . '[]');
    }
}
