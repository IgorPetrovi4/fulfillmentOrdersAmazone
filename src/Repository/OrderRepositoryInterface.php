<?php
declare(strict_types=1);

namespace App\Repository;

use App\Data\AbstractOrder;

interface OrderRepositoryInterface
{
    /**
     * Получает данные заказа по ID
     *
     * @param int $orderId
     * @return AbstractOrder
     */
    public function findOrderById(int $orderId): AbstractOrder;

}
