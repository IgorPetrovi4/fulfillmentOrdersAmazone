<?php
declare(strict_types=1);

namespace App\Service;

use App\Client\AmazonApiClient;
use App\Data\AbstractOrder;
use App\Data\BuyerInterface;
use App\ShippingServiceInterface;
use RuntimeException;

readonly class AmazonShippingService implements ShippingServiceInterface
{
    public function __construct(
        private AmazonApiClient $apiClient
    )
    {
    }

    public function ship(AbstractOrder $order, BuyerInterface $buyer): string
    {
        $order->load();
        $orderData = $order->data;

        if (!$orderData || !isset($orderData['products']) || count($orderData['products']) === 0) {
            throw new RuntimeException('Invalid order data');
        }

        $payload = $this->preparePayload($orderData, $buyer->address);
        $response = $this->apiClient->createFulfillmentOrder($payload);

        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Failed to create fulfillment order');
        }

        $fulfillmentOrder = $this->apiClient->getFulfillmentOrder($orderData['order_unique']);
        $fulfillmentShipments = $fulfillmentOrder['fulfillmentShipments'];

        $packageNumber = null;
        foreach ($fulfillmentShipments as $shipment) {
            foreach ($shipment['fulfillmentShipmentItem'] as $item) {
                $packageNumber = $item['packageNumber'];
                break 2;
            }
        }

        if ($packageNumber === null) {
            throw new RuntimeException('Package number not found');
        }

        $trackingDetails = $this->apiClient->getPackageTrackingDetails($packageNumber);

        if (empty($trackingDetails['trackingNumber'])) {
            throw new RuntimeException('Failed to retrieve tracking number');
        }

        return $trackingDetails['trackingNumber'];
    }

    private function preparePayload(array $orderData, string $address): array
    {
        $items = array_map(function ($product){
            return [
                'sellerSku' => $product['sku'],
                'quantity' => $product['ammount'],
            ];
        }, $orderData['products']);

        $addressPattern = '/^(.*)\n(.*)\n(.*)\n(.*)\n(\d{5}) (.*)\n\n$/';
        if (!preg_match($addressPattern, $address, $matches)) {
            throw new RuntimeException('Invalid buyer address format');
        }

        return [
            'sellerFulfillmentOrderId' => $orderData['order_unique'],
            'displayableOrderId' => $orderData['order_unique'],
            'displayableOrderDate' => gmdate('c'),
            'displayableOrderComment' => 'Order created via API',
            'shippingSpeedCategory' => 'Standard',
            'destinationAddress' => [
                'name' => $matches[1],
                'addressLine1' => $matches[1] . ' ' . $matches[2],
                'city' => $matches[3],
                'stateOrRegion' => $matches[4],
                'postalCode' => $matches[5],
                'countryCode' => 'US',
            ],
            'items' => $items,
        ];
    }
}