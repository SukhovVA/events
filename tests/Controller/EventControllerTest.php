<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/events');
        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $data = json_decode($responseContent, true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('meta', $data);
    }

    public function testShowNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/events/nonexistent');
        $this->assertResponseStatusCodeSame(404);
    }
}
