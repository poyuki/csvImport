<?php

declare(strict_types=1);

namespace App\Application\Service\Entity;

use App\Application\Contract\ReportInterface;
use App\Application\Service\Data\StockData;

readonly class StockImportReport implements ReportInterface
{
    /**
     * @param array<StockData> $failedRowsDetails
     */
    public function __construct(
        public int $successfulImportCount,
        public int $failedImportCount,
        public array $failedRowsDetails
    ) {
    }

    public function toString(): string
    {
        $failedRowsDetails = '';
        foreach ($this->failedRowsDetails as $row) {
            $failedRowsDetails .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $row->productCode,
                $row->productName,
                $row->productDescription,
                $row->stock,
                $row->cost,
                $row->discontinued
            );
        }

        return <<<ROW
Successfully imported stock count: {$this->successfulImportCount}
Failed imported stock count: {$this->failedImportCount}
Failed rows details:
{$failedRowsDetails}
ROW;
    }
}
