<?php

namespace App\Controller\Private;

use App\Controller\BaseController;
use App\DTO\EventRequest;
use App\Response\EventIndexResponse;
use App\Response\Private\EventResponse;
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
        return $this->success([
            'data' => new EventResponse($this->eventService->getEventOrFail($id))
        ]);
    }

    #[Route(methods: 'POST')]
    public function create(#[MapRequestPayload] EventRequest $request): JsonResponse
    {
        $event = $this->eventService->create($request);

        return $this->success(['data' => new EventResponse($event)]);
    }

    #[Route(path: '/{id}', methods: ['PUT'])]
    public function update(string $id, #[MapRequestPayload] EventRequest $request): JsonResponse
    {
        return $this->success([
            'data' => new EventResponse($this->eventService->update($id, $request))
        ]);
    }

    #[Route(path: '/{id}', methods: 'DELETE')]
    public function delete(string $id): JsonResponse
    {
        $this->eventService->delete($id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
