<?php
declare(strict_types=1);

namespace App\Tests\Controller\Private;

use AllowDynamicProperties;
use App\Entity\User;
use App\Factory\EventFactory;
use App\Service\Private\EventService;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

#[AllowDynamicProperties]
class EventControllerTest extends WebTestCase
{
    use Factories;
    private MockObject|EventService $eventService;
    private User $testUser;

    protected function setUp(): void
    {
        $this->eventService = $this->createMock(EventService::class);

        $this->testUser = (new User())
            ->setEmail('test@test.com')
            ->setUuid('12ab6a71-ca4e-4cf4-8026-9f19f7818001')
            ->setRoles(['ROLE_ADMIN']);
    }

    public function testIndex(): void
    {
        // Arrange
        $client = $this->getKernelBrowser();
        $dummyEvents = EventFactory::new()->withoutPersisting()->many(2)->create();
        $page = 1;
        $meta = ['page' => $page, 'total' => 2];

        $this->eventService->expects($this->once())
            ->method('getEvents')
            ->with($page)
            ->willReturn([
                'data' => $dummyEvents,
                'meta' => $meta,
            ]);

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
        $client = $this->getKernelBrowser();
        $dummyEvent = EventFactory::new()->withoutPersisting()->create();
        $testId = 123;
        $this->eventService->expects($this->once())
            ->method('getEventOrFail')
            ->with($testId)
            ->willReturn($dummyEvent);

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
        $client = $this->getKernelBrowser();
        $dummyEvent = EventFactory::new()->withoutPersisting()->create();
        $payload = [
            'name'        => $dummyEvent->getName(),
            'description' => $dummyEvent->getDescription(),
            'startsAt'    => $dummyEvent->getStartsAt()->format('Y-m-d H:i:s'),
            'endsAt'      => $dummyEvent->getEndsAt()->format('Y-m-d H:i:s'),
            'remoteLink'  => $dummyEvent->getRemoteLink(),
        ];

        $this->eventService->expects($this->once())
            ->method('create')
            ->willReturn($dummyEvent);

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

        $data = $content['data'];
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($dummyEvent->getName(), $data['name']);
        $this->assertEquals($dummyEvent->getDescription(), $data['description']);
        $this->assertEquals($dummyEvent->getStartsAt()->format('Y-m-d\TH:i:sP'), $data['starts_at']);
        $this->assertEquals($dummyEvent->getEndsAt()->format('Y-m-d\TH:i:sP'), $data['ends_at']);
        $this->assertEquals($dummyEvent->getRemoteLink(), $data['remote_link']);
        $this->assertEquals($dummyEvent->getAcademicHours(), $data['academic_hours']);
        $this->assertEquals($dummyEvent->getSlug(), $data['slug']);
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

    /**
     * @return KernelBrowser
     */
    protected function getKernelBrowser(): KernelBrowser
    {
        $client = static::createClient();
        $client->loginUser($this->testUser);
        $container = $client->getContainer();
        $container->set(EventService::class, $this->eventService);
        return $client;
    }
}
