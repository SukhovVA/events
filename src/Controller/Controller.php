<?php

namespace App\Controller;

use Psr\Cache\CacheItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

class Controller extends AbstractController
{
    #[Route('health', name: 'health', methods: ['GET'])]
    public function health(CacheInterface $cache): JsonResponse
    {
        $data = $cache->get('health', function (CacheItemInterface $item) {
            $item->expiresAfter(3600);

            return ['health' => true];
        });

        return $this->json([
            'success' => true,
            'status' => 'OK',
            'data' => $data
        ]);
    }
}
