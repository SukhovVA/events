<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\EventService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class EventServiceTest extends TestCase
{
    private EventRepository $eventRepository;
    private TagAwareCacheInterface $cache;
    private EventService $eventService;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->cache = $this->createMock(TagAwareCacheInterface::class);
        $this->eventService = new EventService($this->eventRepository, $this->cache);
    }

    public function testGetEventsReturnsDataFromCache(): void
    {
        $user = $this->createMock(UserInterface::class);
        $page = 1;
        $expectedData = ['data' => ['event1', 'event2'], 'meta' => ['page' => 1]];

        $this->cache->expects($this->once())
            ->method('get')
            ->with("events_$page", $this->isType('callable'))
            ->willReturn($expectedData);

        $result = $this->eventService->getEvents($user, $page);
        $this->assertEquals($expectedData, $result);
    }

    public function testGetActiveEventOrFailReturnsEvent(): void
    {
        $event = new Event();
        $event
            ->setActive(true)
            ->setName('Test Event');

        $this->eventRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '123', 'active' => true])
            ->willReturn($event);

        $result = $this->eventService->getActiveEventOrFail('123');
        $this->assertSame($event, $result);
    }

    public function testGetActiveEventOrFailThrowsNotFound(): void
    {
        $this->eventRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 'nonexistent', 'active' => true])
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Event not found');
        $this->eventService->getActiveEventOrFail('nonexistent');
    }
}
