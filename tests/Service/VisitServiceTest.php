<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Visit;
use App\Exception\VisitExistException;
use App\Repository\VisitRepository;
use App\Service\Visit\RegistrationVisitFactory;
use App\Service\Visit\VisitService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class VisitServiceTest extends TestCase
{
    private MockObject $visitRepository;
    private MockObject $eventDispatcher;
    private VisitService $visitService;

    /**
     * Создаем моки для репозитория и диспетчера событий
     * @return void
     */
    protected function setUp(): void
    {
        $this->visitRepository = $this->createMock(VisitRepository::class);
        $factory = new RegistrationVisitFactory();
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->visitService = new VisitService($this->visitRepository, $factory, $this->eventDispatcher);
    }

    /**
     * Создает фиктивного пользователя.
     */
    private function createDummyUser(): User
    {
        return $this->createMock(User::class);
    }

    /**
     * Создает новое событие.
     */
    private function createDummyEvent(): Event
    {
        return new Event();
    }

    /**
     * Устанавливает ожидание, что для данного пользователя и события метод findExistingVisit вернет null.
     */
    private function expectNoExistingVisit(User $user, Event $event): void
    {
        $this->visitRepository->expects($this->once())
            ->method('findExistingVisit')
            ->with($user, $event)
            ->willReturn(null);
    }

    /**
     * Устанавливает ожидание, что для данного пользователя и события метод findExistingVisit вернет переданный объект visit.
     */
    private function expectExistingVisit(User $user, Event $event, Visit $visit): void
    {
        $this->visitRepository->expects($this->once())
            ->method('findExistingVisit')
            ->with($user, $event)
            ->willReturn($visit);
    }

    /**
     * Тестирует, что при отсутствии существующего визита метод register создает новый объект Visit.
     */
    public function testRegisterCreatesVisit(): void
    {
        // Arrange
        $user = $this->createDummyUser();
        $event = $this->createDummyEvent();

        $this->expectNoExistingVisit($user, $event);

        // Ожидаем вызов метода save() в репозитории
        $this->visitRepository->expects($this->once())
            ->method('save');
        // Ожидаем, что событие регистрации будет отправлено
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch');

        // Act
        $visit = $this->visitService->register($user, $event);

        // Assert
        $this->assertInstanceOf(Visit::class, $visit);
    }

    /**
     * Тестирует, что если визит уже существует, метод register выбрасывает исключение VisitExistException.
     */
    public function testRegisterThrowsExceptionIfVisitExists(): void
    {
        // Arrange
        $user = $this->createDummyUser();
        $event = $this->createDummyEvent();
        $existingVisit = new Visit();

        $this->expectExistingVisit($user, $event, $existingVisit);

        // Assert
        $this->expectException(VisitExistException::class);

        // Act
        $this->visitService->register($user, $event, []);
    }

    /**
     * Тестирует, что метод rate обновляет рейтинг визита, если визит найден и помечен как посещенный.
     */
    public function testRateUpdatesRating(): void
    {
        // Arrange
        $user = $this->createDummyUser();
        $event = $this->createDummyEvent();

        // Создаем mock для Visit, переопределяя только метод isVisited()
        $visit = $this->getMockBuilder(Visit::class)
            ->onlyMethods(['isVisited'])
            ->getMock();
        $visit->method('isVisited')->willReturn(true);
        // Устанавливаем начальное значение рейтинга
        $visit->setRating(0);

        $this->visitRepository->expects($this->once())
            ->method('findExistingVisit')
            ->with($user, $event)
            ->willReturn($visit);
        $this->visitRepository->expects($this->once())
            ->method('save');

        // Act
        $result = $this->visitService->rate($user, $event, 5);

        // Assert
        $this->assertSame($visit, $result);
        $this->assertEquals(5, $result->getRating());
    }

    /**
     * Тестирует, что если визит не найден, метод rate выбрасывает исключение VisitExistException.
     */
    public function testRateThrowsExceptionIfVisitNotFound(): void
    {
        // Arrange
        $user = $this->createDummyUser();
        $event = $this->createDummyEvent();

        $this->visitRepository->expects($this->once())
            ->method('findExistingVisit')
            ->with($user, $event)
            ->willReturn(null);

        // Assert
        $this->expectException(VisitExistException::class);

        // Act
        $this->visitService->rate($user, $event, 3);
    }
}
