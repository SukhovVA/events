<?php

namespace App\Controller\Private;

use App\Controller\BaseController;
use App\DTO\EventRequest;
use App\Response\EventIndexResponse;
use App\Service\Private\EventService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events')]
class EventController extends BaseController
{
    public function __construct(private readonly EventService $eventService) {}

    #[Route(methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): JsonResponse
    {
        $data = $this->eventService->getEvents(page: $page);

        return $this->success([
            'data' => new EventIndexResponse($data['data']),
            'meta' => $data['meta']
        ]);
    }

    #[Route(path: '/{id}', methods: 'GET')]
    public function show(string $id): JsonResponse
    {
        $event = $this->eventService->getEventOrFail($id);

        return $this->success(['data' => $event]);
    }

    #[Route(methods: 'POST')]
    public function create(#[MapRequestPayload] EventRequest $request): JsonResponse
    {
        $event = $this->eventService->create($request);

        return $this->success(['data' => $event]);
    }

    #[Route(path: '/{id}', methods: ['PUT'])]
    public function update(string $id, #[MapRequestPayload] EventRequest $request): JsonResponse
    {
        $event = $this->eventService->getEventOrFail($id);
        $this->eventService->update($event, $request);

        return $this->success(['data' => $event]);
    }

    #[Route(path: '/{id}', methods: 'DELETE')]
    public function delete(string $id): JsonResponse
    {
        $event = $this->eventService->getEventOrFail($id);
        $this->eventService->delete($event);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
