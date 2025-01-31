<?php

namespace App\Controller;

use App\Entity\User;
use App\Response\EventIndexResponse;
use App\Response\EventResponse;
use App\Service\EventService;
use App\Service\MailService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User getUser()
 */
#[Route('/events')]
class EventController extends AbstractController
{
    /**
     * Получает список мероприятий с пагинацией.
     *
     * Для текущего аутентифицированного пользователя выдается дополненный пул данных.
     * Поддерживает пагинацию через параметр `page`, который указывает текущую страницу.
     *
     * @param int $page Номер страницы для пагинации (по умолчанию 1).
     *
     * @return JsonResponse Возвращает JSON-ответ, содержащий:
     *   - success: boolean, указывает на успешность выполнения запроса
     *   - data: объект, содержащий список мероприятий
     *   - meta: метаданные
     *
     * @throws AccessDeniedException Ошибка аутентификации.
     * @throws InvalidArgumentException
     */
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

    /**
     * Получает детали конкретного мероприятия по его slug.
     *
     * Этот метод возвращает детали мероприятия, если оно активно и существует.
     *
     * @param string $slug Уникальный идентификатор события (slug).
     *
     * @return JsonResponse Возвращает JSON-ответ, содержащий:
     *   - success: boolean, указывает на успешность выполнения запроса
     *   - data: объект, содержащий детали мероприятия
     *
     * @throws NotFoundHttpException Если мероприятие не найдено.
     */
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

    /**
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/{slug}/register', methods: 'POST')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function register(
        string       $slug,
        EventService $eventService,
        MailService  $mailService,
    ): JsonResponse
    {
        $event = $eventService->getActiveEvent($slug);
        $user = $this->getUser();

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        $eventService->register($event, $user);
        $mailService->sendRegistrationEmail($user->getEmail());

        return $this->json([
            'success' => true
        ]);
    }
}
