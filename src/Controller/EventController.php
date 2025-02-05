<?php

namespace App\Controller;

use App\DTO\RateRequestDTO;
use App\Exception\VisitExistException;
use App\Response\EventIndexResponse;
use App\Response\EventResponse;
use App\Service\EventService;
use App\Service\Visit\VisitService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/events')]
class EventController extends BaseController
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

        return $this->success([
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
    #[Route(path: '/{id}', methods: 'GET')]
    public function show(
        string       $id,
        EventService $eventService,
    ): JsonResponse
    {
        $event = $eventService->getActiveEventOrFail($id);

        return $this->success([
            'data' => new EventResponse($event)
        ]);
    }

    /**
     * Регистрация пользователя на мероприятие.
     *
     * @param string $slug Уникальный идентификатор события (slug).
     *
     * @return JsonResponse Возвращает JSON-ответ, содержащий:
     * - success: boolean, указывает на успешность выполнения запроса
     *
     * @throws VisitExistException
     */
    #[Route(path: '/{id}/register', methods: 'POST')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function register(
        string       $id,
        EventService $eventService,
        VisitService $visitService,
    ): JsonResponse
    {
        $event = $eventService->getActiveEventOrFail($id);
        $visitService->register($this->getUser(), $event);

        return $this->success();
    }

    /**
     * Оценка посещенного мероприятия.
     *
     * @param string $slug Уникальный идентификатор события (slug).
     *
     * @return JsonResponse Возвращает JSON-ответ, содержащий:
     * - success: boolean, указывает на успешность выполнения запроса
     *
     * @throws VisitExistException
     */
    #[Route(path: '/{id}/rate', methods: 'POST')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function rate(
        string                              $id,
        #[MapRequestPayload] RateRequestDTO $request,
        EventService                        $eventService,
        VisitService                        $visitService,
    ): JsonResponse
    {
        $event = $eventService->getActiveEventOrFail($id);
        $visitService->rate($this->getUser(), $event, $request->rating);

        return $this->success();
    }
}
