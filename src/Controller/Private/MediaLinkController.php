<?php

namespace App\Controller\Private;

use App\Controller\BaseController;
use App\DTO\MediaLinkRequest;
use App\Service\Private\MediaLinkService;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/media')]
class MediaLinkController extends BaseController {
    public function __construct(private readonly MediaLinkService $mediaLinkService) {}

    /**
     * @throws FilesystemException
     */
    #[Route(methods: 'POST')]
    public function upload(#[MapRequestPayload] MediaLinkRequest $request): JsonResponse
    {
        $media = $this->mediaLinkService->save($request);

        return $this->success(['data' => $media]);
    }

    /**
     * @throws FilesystemException
     */
    #[Route(path: '/{id}', methods: 'DELETE')]
    public function delete(string $id): JsonResponse
    {
        $this->mediaLinkService->delete($id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
