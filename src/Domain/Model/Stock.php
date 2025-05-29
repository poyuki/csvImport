<?php

declare(strict_types=1);

namespace App\Domain\Model;

use DateTimeInterface;

final class Stock
{
    private int $productDataId;
    private DateTimeInterface $timestamp;

    public function __construct(
        private string $productName,
        private string $productDesc,
        private string $productCode,
        private int $stock,
        private float $cost,
        private ?DateTimeInterface $addedAt = new \DateTimeImmutable(),
        private ?DateTimeInterface $discontinuedAt = null,
    ) {
    }


    public function isStorable(): bool
    {
        if ($this->cost < 5 && $this->stock < 10) {
            return false;
        }

        if ($this->cost > 10000) {
            return false;
        }

        return true;
    }

    public function update(
        string $productName,
        string $productDesc,
        int $stock,
        float $cost,
        ?DateTimeInterface $discontinuedAt
    ): void {
        $this->productName = $productName;
        $this->productDesc = $productDesc;
        $this->stock = $stock;
        $this->cost = $cost;
        $this->discontinuedAt = $discontinuedAt;
    }
}
