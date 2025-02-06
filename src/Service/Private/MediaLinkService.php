<?php

namespace App\Service\Private;

use App\DTO\MediaLinkRequest;
use App\Entity\MediaLink;
use App\Repository\MediaLinkRepository;
use DateTime;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class MediaLinkService {

    public function __construct(
        private FilesystemOperator        $minioStorage,
        private MediaLinkRepository       $mediaLinkRepository,
        private MediaLinkFactoryInterface $mediaLinkFactory,
    ) {}

    public function getOrFail(string $id): ?MediaLink
    {
        $media = $this->mediaLinkRepository->find($id);

        if (!$media) {
            throw new NotFoundHttpException('Media link not found');
        }

        return $media;
    }

    /**
     * @throws FilesystemException
     */
    public function save(MediaLinkRequest $request): MediaLink
    {
        $datePath = (new DateTime())->format('Y/m');
        $hash = hash('sha256', $request->file->getContent());
        $mediaLink = $this->mediaLinkRepository->findOneBy(['hash' => $hash]);

        if (!$mediaLink) {
            $this->minioStorage->write("/uploads/$datePath/{$request->file->getFilename()}", $request->file->getContent());

            $mediaLink = $this->mediaLinkFactory->create($request->file->getFilename(), $request->file->getClientOriginalName(), $hash, $request->type);

            $this->mediaLinkRepository->save($mediaLink);
        }

        return $mediaLink;
    }

    /**
     * @throws FilesystemException
     */
    public function delete(string $id): void
    {
        $mediaLink = $this->getOrFail($id);
        $datePath = $mediaLink->getCreatedAt()->format('Y/m');

        $this->minioStorage->delete("/uploads/$datePath/{$mediaLink->getName()}");
    }
}
