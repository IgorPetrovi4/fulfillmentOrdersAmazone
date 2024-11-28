<?php
declare(strict_types=1);

namespace App\Client;

use GuzzleHttp\Client;
use RuntimeException;

class AmazonApiClient
{
    private Client $client;

    public function __construct(string $baseUri, array $config)
    {
        $this->client = new Client(array_merge(['base_uri' => $baseUri], $config));
    }

    private function getDefaultHeaders(array $additionalHeaders = []): array
    {
        return array_merge([
            'Content-Type' => 'application/json',
        ], $additionalHeaders);
    }

    public function createFulfillmentOrder(array $payload, array $additionalHeaders = []): array
    {
        $response = $this->client->post('/fba/outbound/2020-07-01/fulfillmentOrders', [
            'json' => $payload,
            'headers' => $this->getDefaultHeaders($additionalHeaders),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to create fulfillmentOrders: ' . $response->getBody());
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getFulfillmentOrder(string $sellerFulfillmentOrderId, array $additionalHeaders = []): array
    {
        $response = $this->client->get('/fba/outbound/2020-07-01/fulfillmentOrders/' . $sellerFulfillmentOrderId, [
            'headers' => $this->getDefaultHeaders($additionalHeaders),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to get fulfillment order: ' . $response->getBody());
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getPackageTrackingDetails(int $packageNumber, array $additionalHeaders = []): array
    {
        $response = $this->client->get('/fba/outbound/2020-07-01/tracking', [
            'query' => ['packageNumber' => $packageNumber],
            'headers' => $this->getDefaultHeaders($additionalHeaders),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to get tracking details: ' . $response->getBody());
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}