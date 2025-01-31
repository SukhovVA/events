<?php

namespace App\Controller;

use App\Response\EventIndexResponse;
use App\Response\EventResponse;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events')]
class EventController extends AbstractController
{
    #[Route(methods: 'GET')]
    public function index(
        EventService             $eventService,
        #[MapQueryParameter] int $page = 1,
    ): JsonResponse
    {
        $data = $eventService->getEvents($this->getUser(), $page);

        return $this->json([
            'success' => true,
            'data'    => new EventIndexResponse($data['data']),
            'meta'    => $data['meta']
        ]);
    }

    #[Route(path: '/{slug}', methods: 'GET')]
    public function show(
        string       $slug,
        EventService $eventService,
    ): JsonResponse
    {
        $event = $eventService->getActiveEvent($slug);

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        return $this->json([
            'success' => true,
            'data' => new EventResponse($event)
        ]);
    }
}
