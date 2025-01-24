<?php

namespace App\Security;

use App\Exception\AccessDeniedException;
use App\Gateway\ProsvGateway;
use App\Service\Hmac;
use App\Service\UserCreator;
use App\Service\UserService;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserCreator $userCreator,
        private UserService $userService,
        private ProsvGateway $prosvGateway,
        private string $accessSecret,
    ) {}

    /**
     * @throws Exception
     */
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $authenticate = false;
        $payload = null;

        $bearerToken = explode('.', $accessToken);

        if (count($bearerToken) == 3) {
            $jwtArr = array_combine(['header', 'payload', 'signature'], $bearerToken);

            $payload = json_decode(base64_decode($jwtArr['payload']));

            $hmac = new Hmac();
            $data = sprintf("%s.%s", $jwtArr['header'], $jwtArr['payload']);
            $hash = $hmac->sign($data, $this->accessSecret);
            $authenticate = hash_equals($hash, $jwtArr['signature']) && !$this->isExpired($payload->exp);
        }

        if (!$authenticate) {
            throw new AccessDeniedException();
        }

        return new UserBadge(
            $payload->sub,
            function (string $userIdentifier): ?UserInterface
            {
                $data = $this->prosvGateway->getUserDetails($userIdentifier);

                if (!isset($data)) {
                    throw new AccessDeniedException();
                }

                return $this->userCreator->createUser($data);
            }
        );
    }

    /**
     * Проверка определяющая момент, когда токен станет невалидным по времени
     * @param $timestamp
     * @return bool
     * @throws Exception
     */
    protected function isExpired($timestamp): bool
    {
        $expiration = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->setTimestamp($timestamp);
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        return $expiration < $now;
    }
}
