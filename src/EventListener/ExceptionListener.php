<?php

namespace App\EventListener;

use App\Exception\AppException;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $payload = [
            'success' => false,
            'error' => $exception->getMessage(),
        ];

        $response = new JsonResponse();

        switch (true) {
            case $exception instanceof HttpExceptionInterface:
                $response->setStatusCode($exception->getStatusCode());
                break;

            case $exception instanceof EntityNotFoundException:
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);

                break;
            case $exception instanceof AppException :
                $response->setStatusCode($exception->getCode());
                break;
            default :
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->setContent(json_encode($payload));

        $event->setResponse($response);
    }
}
