<?php
declare(strict_types=1);

namespace App\Repository;

use App\Data\BuyerInterface;

interface BuyerRepositoryInterface
{
    /**
     * Получает данные покупателя по ID
     *
     * @param int $buyerId
     * @return BuyerInterface
     */
    public function findBuyerById(int $buyerId): BuyerInterface;

}