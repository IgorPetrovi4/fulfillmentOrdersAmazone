<?php
declare(strict_types=1);

namespace App\Data;

use App\Repository\FileOrderRepository;

class Order extends AbstractOrder
{
    protected function loadOrderData(int $id): array
    {
        $dataPath = __DIR__ . '/../../mock';
        $orderRepository = new FileOrderRepository($dataPath);

        $order = $orderRepository->findOrderById($id);

        return $order->data;
    }
}