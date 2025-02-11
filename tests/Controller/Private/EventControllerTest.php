<?php
declare(strict_types=1);

namespace App\Tests\Controller\Private;

use AllowDynamicProperties;
use App\Entity\Event;
use App\Entity\MediaLink;
use App\Entity\User;
use App\Service\Private\EventService;
use DateTime;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

#[AllowDynamicProperties]
class EventControllerTest extends WebTestCase
{
    private MockObject|EventService $eventService;
    private User $testUser;
    private Event $dummyEvent;

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
            ->setCreatedAt(new DateTime())
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

        $client = static::createClient();
        $client->loginUser($this->testUser);
        $container = $client->getContainer();
        $container->set(EventService::class, $this->eventService);

        // Act
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

        $client = static::createClient();
        $client->loginUser($this->testUser);
        $container = $client->getContainer();
        $container->set(EventService::class, $this->eventService);

        // Act

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

    public function testCreate(): void
    {
        // Arrange
        $payload = [
            'name'        => $this->dummyEvent->getName(),
            'description' => $this->dummyEvent->getDescription(),
            'startsAt'    => $this->dummyEvent->getStartsAt()->format('Y-m-d H:i:s'),
            'endsAt'      => $this->dummyEvent->getEndsAt()->format('Y-m-d H:i:s'),
            'remoteLink'  => $this->dummyEvent->getRemoteLink(),
        ];

        $this->eventService->expects($this->once())
            ->method('create')
            ->willReturn($this->dummyEvent);

        $client = static::createClient();
        $client->loginUser($this->testUser);
        $container = $client->getContainer();
        $container->set(EventService::class, $this->eventService);

        // Act
        $client->jsonRequest(
            method: 'POST',
            uri: '/api/v1/private/events',
            parameters: $payload
        );
        $response = $client->getResponse();

        // Assert
        $this->assertResponseIsSuccessful();
        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals($this->dummyEvent->getName(), $content['data']['name']);

        $data = $content['data'];
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($this->dummyEvent->getName(), $data['name']);
        $this->assertEquals($this->dummyEvent->getDescription(), $data['description']);
        $this->assertEquals($this->dummyEvent->getStartsAt()->format('Y-m-d\TH:i:sP'), $data['starts_at']);
        $this->assertEquals($this->dummyEvent->getEndsAt()->format('Y-m-d\TH:i:sP'), $data['ends_at']);
        $this->assertEquals($this->dummyEvent->getRemoteLink(), $data['remote_link']);
        $this->assertEquals($this->dummyEvent->getAcademicHours(), $data['academic_hours']);
        $this->assertEquals($this->dummyEvent->getSlug(), $data['slug']);
    }

    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request($httpMethod, $url);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function getUrlsForRegularUsers(): Generator
    {
        yield ['GET', '/api/v1/private/events'];
        yield ['GET', '/api/v1/private/events/1'];
        yield ['POST', '/api/v1/private/events'];
        yield ['PUT', '/api/v1/private/events/1'];
        yield ['DELETE', '/api/v1/private/events/1'];
    }
}
