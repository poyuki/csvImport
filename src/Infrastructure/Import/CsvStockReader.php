<?php

declare(strict_types=1);

namespace App\Infrastructure\Import;

use App\Application\Contract\StocksReaderInterface;
use App\Application\Service\Data\StockData;
use Generator;
use League\Csv\Reader;

class CsvStockReader implements StocksReaderInterface
{
    /**
     * @inheritDoc
     */
    public function readFromStream(mixed $stream): iterable
    {
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0);
        $header = $csv->getHeader();
        $records = $csv->getRecords($header);

        foreach ($records as $record) {
            yield new StockData(
                $record['Product Code'],
                $record['Product Name'],
                $record['Product Description'],
                $record['Stock'],
                $record['Cost in GBP'],
                $record['Discontinued'],
            );
        }
    }
}
