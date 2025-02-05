<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends BaseController
{
    #[Route('health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return $this->success([
            'status' => 'OK'
        ]);
    }
}
