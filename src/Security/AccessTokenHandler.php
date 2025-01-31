<?php

namespace App\Security;

use App\DTO\TokenDTO;
use App\Exception\AccessDeniedException;
use App\Gateway\ProsvGateway;
use App\Service\Hmac;
use App\Service\UserCreator;
use App\Service\UserService;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws AccessDeniedException
     */
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            $tokenDTO = $this->decodeToken($accessToken);
            $this->validateToken($tokenDTO);
            return $this->createUserBadge($tokenDTO->decodedPayload->sub);
        } catch (Exception $e) {
            $this->logger->error('Failed to authenticate token: ' . $e->getMessage());
            throw new AccessDeniedException('Invalid token');
        }
    }

    /**
     * Декодирует токен и возвращает TokenDTO.
     *
     * @throws AccessDeniedException
     */
    private function decodeToken(string $accessToken): TokenDTO
    {
        $bearerToken = explode('.', $accessToken);
        if (count($bearerToken) !== 3) {
            throw new AccessDeniedException('Invalid token format');
        }

        $jwtArr = array_combine(['header', 'payload', 'signature'], $bearerToken);

        $decodedPayload = json_decode(base64_decode($jwtArr['payload']));
        if ($decodedPayload === null) {
            throw new AccessDeniedException('Invalid token payload');
        }

        return new TokenDTO($jwtArr['header'], $jwtArr['payload'], $jwtArr['signature'], $decodedPayload);
    }

    /**
     * Проверяет валидность токена (срок действия и подпись).
     *
     * @throws AccessDeniedException|Exception
     */
    private function validateToken(TokenDTO $tokenDTO): void
    {
        if ($this->isExpired($tokenDTO->decodedPayload->exp)) {
            throw new AccessDeniedException('Token expired');
        }

        $hmac = new Hmac();
        $data = sprintf("%s.%s", $tokenDTO->header, $tokenDTO->payload);
        $hash = $hmac->sign($data, $this->accessSecret);

        if (!hash_equals($hash, $tokenDTO->signature)) {
            throw new AccessDeniedException('Invalid token signature');
        }
    }

    /**
     * Создает UserBadge для пользователя.
     *
     * @throws AccessDeniedException
     */
    private function createUserBadge(string $userIdentifier): UserBadge
    {
        return new UserBadge(
            $userIdentifier,
            function (string $userIdentifier): ?UserInterface {
                $user = $this->userService->getUser($userIdentifier);
                if (!$user) {
                    $data = $this->prosvGateway->getUserDetails($userIdentifier);
                    if (!$data) {
                        throw new AccessDeniedException('User not found');
                    }
                    $user = $this->userCreator->createUser($data);
                }
                return $user;
            }
        );
    }

    /**
     * Проверяет, истек ли срок действия токена.
     * @throws Exception
     */
    private function isExpired(int $timestamp): bool
    {
        $expiration = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->setTimestamp($timestamp);
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        return $expiration < $now;
    }
}
