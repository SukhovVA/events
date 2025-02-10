<?php
declare(strict_types=1);

namespace App\Tests\Controller\Private;

use App\Entity\Event;
use App\Entity\MediaLink;
use App\Entity\User;
use App\Service\Private\EventService;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

#[\AllowDynamicProperties]
class EventControllerTest extends WebTestCase
{
    private ?EventService $eventService = null;
    private MockObject|User $testUser;

    protected function setUp(): void
    {
        $this->eventService = $this->createMock(EventService::class);

        $this->testUser = (new User())
            ->setEmail('test@test.com')
            ->setUuid('12ab6a71-ca4e-4cf4-8026-9f19f7818001')
            ->setRoles(['ROLE_ADMIN']);

        $this->dummyEvent = new Event();

        $this->dummyEvent->setName('Dummy Event')
            ->setDescription('This is a dummy event for testing purposes.')
            ->setStartsAt(new DateTimeImmutable('2025-01-01 10:00:00'))
            ->setEndsAt(new DateTimeImmutable('2025-01-01 12:00:00'))
            ->setAcademicHours(2.0)
            ->setRemoteLink('https://example.com/event')
            ->setSlug('dummy-event')
            ->setActive(true);

        $dummyMediaLink = new MediaLink();
        $dummyMediaLink
            ->setCreatedAt(new \DateTime())
            ->setName('Medialink Test Name')
            ->setOriginalName('Medialink Test Original Name')
            ->setType(1);

        $this->dummyEvent->setCover($dummyMediaLink);
    }

    public function testIndex(): void
    {
        // Arrange
        $page = 1;
        $dummyEvents = [$this->dummyEvent];
        $meta = ['page' => $page, 'total' => 2];

        $this->eventService->expects($this->once())
            ->method('getEvents')
            ->with($page)
            ->willReturn([
                'data' => $dummyEvents,
                'meta' => $meta,
            ]);

        // Act
        $client = static::createClient();
        $client->loginUser($this->testUser);
        $container = $client->getContainer();
        $container->set(EventService::class, $this->eventService);

        $client->request('GET', '/api/v1/private/events');
        $response = $client->getResponse();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('meta', $content);

        $this->assertIsArray($content['data']);
        $this->assertNotEmpty($content['data']);

        $firstEvent = $content['data'][0];
        $this->assertArrayHasKey('id', $firstEvent);
        $this->assertArrayHasKey('slug', $firstEvent);
        $this->assertArrayHasKey('name', $firstEvent);
        $this->assertArrayHasKey('starts_at', $firstEvent);
        $this->assertArrayHasKey('ends_at', $firstEvent);
        $this->assertArrayHasKey('cover', $firstEvent);
    }

    public function testShow(): void
    {
        // Arrange
        $testId = 123;
        $this->eventService->expects($this->once())
            ->method('getEventOrFail')
            ->with($testId)
            ->willReturn($this->dummyEvent);

        // Act
        $client = static::createClient();
        $client->loginUser($this->testUser);
        $container = $client->getContainer();
        $container->set(EventService::class, $this->eventService);
        $client->request('GET', "/api/v1/private/events/$testId");
        $response = $client->getResponse();

        // Assert
        $this->assertResponseIsSuccessful();
        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);

        $data = $content['data'];
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('starts_at', $data);
        $this->assertArrayHasKey('ends_at', $data);
        $this->assertArrayHasKey('cover', $data);
    }

    public function testUnauthorizedResponse()
    {
        // Arrange
        $page = 1;
        $dummyEvents = [$this->dummyEvent];
        $meta = ['page' => $page, 'total' => 2];

        $this->eventService->expects($this->never())
            ->method('getEvents')
            ->with($page)
            ->willReturn([
                'data' => $dummyEvents,
                'meta' => $meta,
            ]);

        // Act
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set(EventService::class, $this->eventService);
        $client->request('GET', '/api/v1/private/events');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
