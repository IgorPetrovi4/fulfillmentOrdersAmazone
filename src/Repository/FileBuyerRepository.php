<?php
declare(strict_types=1);

namespace App\Repository;

use App\Data\Buyer;
use App\Data\BuyerInterface;
use RuntimeException;

class FileBuyerRepository implements BuyerRepositoryInterface
{
    private string $dataPath;

    public function __construct(string $dataPath)
    {
        $this->dataPath = rtrim($dataPath, '/');
    }

    public function findBuyerById(int $buyerId): BuyerInterface
    {
        $filePath = "{$this->dataPath}/buyer.$buyerId.json";

        if (!file_exists($filePath)) {
            throw new RuntimeException("Buyer file not found: $filePath");
        }

        $buyerData = json_decode(file_get_contents($filePath), true);
        if (!$buyerData) {
            throw new RuntimeException("Invalid JSON in buyer file: $filePath");
        }

        return new Buyer($buyerData);
    }
}