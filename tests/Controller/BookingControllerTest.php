<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\CsvService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\MockObject\MockObject;
class BookingControllerTest extends WebTestCase
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

    public function testCreateBookingSuccess(): void
    {
        $this->csvServiceMock->method('readAll')
            ->willReturn([
                ['id', 'houseId', 'phone', 'comment'],
                [1, 1, '+79992233444', 'test1'],
                [2, 2, '89991122333', 'test2']
            ]);

        $this->csvServiceMock->expects($this->once())
            ->method('append')
            ->with(
                'bookings.csv',
                [3, 1, '+71234567890', 'New booking']
            );

        $this->csvServiceMock->method('readAll')
            ->willReturnOnConsecutiveCalls(
                [
                    ['id', 'name'],
                    [1, 'House 1'],
                    [2, 'House 2']
                ],
                [
                    ['id', 'houseId', 'phone', 'comment'],
                    [1, 1, '+79992233444', 'test1'],
                    [2, 2, '89991122333', 'test2']
                ]
            );

        $payload = [
            'houseId' => 1,
            'phone' => '+71234567890',
            'comment' => 'New booking'
        ];

        $this->client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('ok', $response['status']);
        $this->assertEquals(3, $response['id']);
    }

    public function testCreateBookingInvalidPhone(): void
    {
        $payload = [
            'houseId' => 1,
            'phone' => 'invalid!@#phone',
            'comment' => 'Test'
        ];

        $this->client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            '{"error":"Invalid input"}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreateBookingMissingHouseId(): void
    {
        $payload = [
            'phone' => '+1234567890',
            'comment' => 'Test'
        ];

        $this->client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            '{"error":"Invalid input"}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreateBookingHouseNotFound(): void
    {
        $this->csvServiceMock->method('readAll')
            ->willReturn([
                ['id', 'name'],
                [1, 'House 1'],
                [2, 'House 2']
            ]);

        $payload = [
            'houseId' => 999,
            'phone' => '+1234567890',
            'comment' => 'Test'
        ];

        $this->client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            '{"error":"House not found"}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testUpdateBookingSuccess(): void
    {
        $this->csvServiceMock->expects($this->once())
            ->method('overwriteRow')
            ->with(
                'bookings.csv',
                1,
                [1, 2, '1234567890', 'Updated comment']
            );

        $this->csvServiceMock->method('readAll')
            ->willReturn([
                ['id', 'name'],
                [1, 'House 1'],
                [2, 'House 2']
            ]);

        $payload = [
            'houseId' => 2,
            'phone' => '1234567890',
            'comment' => 'Updated comment'
        ];

        $this->client->request(
            'PUT',
            '/api/bookings/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('updated', $response['status']);
    }

    public function testUpdateBookingInvalidPhone(): void
    {
        $payload = [
            'houseId' => 1,
            'phone' => 'invalid-phone',
            'comment' => 'Test'
        ];

        $this->client->request(
            'PUT',
            '/api/bookings/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            '{"error":"Invalid input"}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testUpdateBookingHouseNotFound(): void
    {
        $this->csvServiceMock->method('readAll')
            ->willReturn([
                ['id', 'name'],
                [1, 'House 1'],
                [2, 'House 2']
            ]);

        $payload = [
            'houseId' => 999,
            'phone' => '+1234567890',
            'comment' => 'Test'
        ];

        $this->client->request(
            'PUT',
            '/api/bookings/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            '{"error":"House not found"}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testUpdateBookingNotFound(): void
    {
        $this->csvServiceMock->method('overwriteRow')
            ->willThrowException(new \RuntimeException('Row not found'));

        $this->csvServiceMock->method('readAll')
            ->willReturn([
                ['id', 'name'],
                [1, 'House 1'],
                [2, 'House 2']
            ]);

        $payload = [
            'houseId' => 1,
            'phone' => '+1234567890',
            'comment' => 'Test'
        ];

        $this->client->request(
            'PUT',
            '/api/bookings/999',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(404);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
    }
}