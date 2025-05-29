<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Service\Entity\StockImportReport;

interface StockImporterInterface
{
    public function import(mixed $stream, bool $force = true): ReportInterface;
}
