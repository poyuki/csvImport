<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Stock;

interface StockRepositoryInterface
{
    public function findById(int $id): ?Stock;

    public function findByCode(string $code): ?Stock;

    public function save(Stock $stock): void;

    public function bulkSave(iterable $stocks): void;
}
