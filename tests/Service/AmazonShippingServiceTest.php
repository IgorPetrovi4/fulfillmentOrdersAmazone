<?php
declare(strict_types=1);

namespace Tests\Service;

use App\Client\AmazonApiClient;
use App\Data\Order;
use App\Repository\BuyerRepositoryInterface;
use App\Repository\FileBuyerRepository;
use App\Service\AmazonShippingService;
use PHPUnit\Framework\TestCase;

class AmazonShippingServiceTest extends TestCase
{
    private BuyerRepositoryInterface $buyerRepository;

    protected function setUp(): void
    {
        $dataPath = __DIR__ . '/../../mock';
        $this->buyerRepository = new FileBuyerRepository($dataPath);
    }

    public function testShipSuccessfully(): void
    {
        $mockApiClient = $this->createMock(AmazonApiClient::class);
        $mockApiClient
            ->expects($this->once())
            ->method('createFulfillmentOrder')
            ->willReturn(['statusCode' => 200]);

        $mockApiClient
            ->expects($this->once())
            ->method('getFulfillmentOrder')
            ->willReturn([
                'fulfillmentShipments' => [
                    [
                        'fulfillmentShipmentItem' => [
                            ['packageNumber' => 1]
                        ]
                    ]
                ]
            ]);

        $mockApiClient
            ->expects($this->once())
            ->method('getPackageTrackingDetails')
            ->willReturn(['trackingNumber' => '123456789']);

        $shippingService = new AmazonShippingService($mockApiClient);

        $order = new Order(16400);
        $buyer = $this->buyerRepository->findBuyerById(29664);
        $trackingNumber = $shippingService->ship($order, $buyer);

        $this->assertEquals('123456789', $trackingNumber);
    }

    public function testShipWithInvalidOrder(): void
    {
        $orderId = 99999;
        $mockApiClient = $this->createMock(AmazonApiClient::class);

        $shippingService = new AmazonShippingService($mockApiClient);

        $order = new Order($orderId);
        $buyer = $this->buyerRepository->findBuyerById(29664);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Order %d not found !', $orderId));

        $shippingService->ship($order, $buyer);
    }

    public function testShipWithoutProducts(): void
    {
        $mockApiClient = $this->createMock(AmazonApiClient::class);
        $mockApiClient
            ->expects($this->once())
            ->method('createFulfillmentOrder')
            ->willReturn(['statusCode' => 400]);

        $shippingService = new AmazonShippingService($mockApiClient);

        $order = new Order(16400);
        $order->data['products'] = [];

        $buyer = $this->buyerRepository->findBuyerById(29664);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to create fulfillment order');

        $shippingService->ship($order, $buyer);
    }
}