<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Service\Data\StockData;

interface StocksReaderInterface
{
    /**
     * @param mixed $stream
     * @return iterable<StockData>
     */
    public function readFromStream(mixed $stream): iterable;
}
