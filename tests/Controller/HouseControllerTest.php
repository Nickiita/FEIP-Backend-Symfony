<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\CsvService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\MockObject\MockObject;
class HouseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CsvService|MockObject $csvServiceMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->csvServiceMock = $this->createMock(CsvService::class);

        $container = $this->client->getContainer();
        $container->set('App\Service\CsvService', $this->csvServiceMock);
    }

    public function testListHouses(): void
    {
        $this->csvServiceMock->expects($this->once())
            ->method('readAll')
            ->willReturn([
                ['id', 'name', 'price'],
                [1, 'House One', 100],
                [2, 'House Two', 200]
            ]);

        $this->client->request('GET', '/api/houses');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $expectedResponse = [
            ['id' => '1', 'name' => 'House One', 'price' => '100'],
            ['id' => '2', 'name' => 'House Two', 'price' => '200'],
        ];

        $this->assertEquals(
            $expectedResponse,
            json_decode($this->client->getResponse()->getContent(), true)
        );
    }

    public function testListHousesWhenFileEmpty(): void
    {
        $this->csvServiceMock->expects($this->once())
            ->method('readAll')
            ->willReturn([]);

        $this->client->request('GET', '/api/houses');
        $this->assertResponseIsSuccessful();

        $this->assertEquals(
            [],
            json_decode($this->client->getResponse()->getContent(), true)
        );
    }

    public function testListHousesWhenFileNotExists(): void
    {
        $this->csvServiceMock->expects($this->once())
            ->method('readAll')
            ->willReturn([]);

        $this->client->request('GET', '/api/houses');
        $this->assertResponseIsSuccessful();

        $this->assertEquals(
            [],
            json_decode($this->client->getResponse()->getContent(), true)
        );
    }
}