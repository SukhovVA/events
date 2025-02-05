<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method User getUser()
 */
class BaseController extends AbstractController
{
    public function success(array $data = []): JsonResponse
    {
        return $this->json([
            'success' => true,
            ...$data
        ]);
    }

    public function failure(int $code, mixed $error): JsonResponse
    {
        return $this->json([
            'success' => false,
            'error'   => $error
        ], $code);
    }
}
