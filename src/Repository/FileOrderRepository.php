<?php
declare(strict_types=1);

namespace App\Repository;

use App\Data\AbstractOrder;
use App\Data\Order;

class FileOrderRepository implements OrderRepositoryInterface
{
    private string $dataPath;

    public function __construct(string $dataPath)
    {
        $this->dataPath = rtrim($dataPath, '/');
    }

    public function findOrderById(int $orderId): AbstractOrder
    {
        $filePath = "{$this->dataPath}/order.$orderId.json";

        if (!file_exists($filePath)) {
            throw new \RuntimeException(\sprintf('Order %d not found !', $orderId));
        }

        $orderData = json_decode(file_get_contents($filePath), true);
        if (!$orderData) {
            throw new \RuntimeException(\sprintf('Failed to read order file for order %d!', $orderId));
        }

        $order = new Order($orderId);
        $order->data = $orderData;

        return $order;
    }
}
