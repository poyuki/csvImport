<?php

declare(strict_types=1);

namespace App\Application\Service\Validator;

use App\Application\Service\Data\StockData;

interface StockDataValidatorInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(StockData $stockData): void;
}
