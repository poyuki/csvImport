<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Contract\StockImporterInterface;
use App\Application\Contract\StocksReaderInterface;
use App\Application\Service\Entity\StockImportReport;
use App\Application\Service\Validator\StockDataValidatorInterface;
use App\Domain\Model\Stock;
use App\Domain\Repository\StockRepositoryInterface;
use DateTimeImmutable;

readonly class StockImporter implements StockImporterInterface
{

    public function __construct(
        private StocksReaderInterface $stocksReader,
        private StockRepositoryInterface $stockRepository,
        private StockDataValidatorInterface $validator
    ) {
    }

    public function import(mixed $stream, bool $force = true): StockImportReport
    {
        $stocks = $this->stocksReader->readFromStream($stream);
        $failed = [];
        $successCount = 0;
        $failedCount = 0;
        $stocksForInsert = [];
        foreach ($stocks as $stockData) {
            try {
                $this->validator->validate($stockData);
                $stock = $this->stockRepository->findByCode($stockData->productCode);
                if ($stock instanceof Stock) {
                    $stock->update(
                        $stockData->productName,
                        $stockData->productDescription,
                        (int)$stockData->stock,
                        (float)$stockData->cost,
                        discontinuedAt: $stockData->discontinued === 'yes' ? new DateTimeImmutable() : null,
                    );
                } else {
                    $stock = new Stock(
                        $stockData->productName,
                        $stockData->productDescription,
                        $stockData->productCode,
                        (int)$stockData->stock,
                        (float)$stockData->cost,
                        discontinuedAt: $stockData->discontinued === 'yes' ? new DateTimeImmutable() : null,
                    );
                }

                if ($stock->isStorable()) {
                    $stocksForInsert[] = $stock;
                    $successCount++;
                } else {
                    $failedCount++;
                    $failed[] = $stockData;
                }
            } catch (Validator\ValidationException) {
                $failedCount++;
                $failed[] = $stockData;
            }
        }
        if (count($stocksForInsert) > 0 && $force) {
            $this->stockRepository->bulkSave($stocksForInsert);
        }

        return new StockImportReport($successCount, $failedCount, $failed);
    }
}
