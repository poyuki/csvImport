<?php

declare(strict_types=1);

namespace App\Application\Service\Validator;

use App\Application\Service\Data\StockData;
use Exception;
use Throwable;

class ValidationException extends Exception
{
    public function __construct(
        public readonly array $errors = [],
        public readonly ?StockData $stockData = null
    ) {
        parent::__construct("failed validation", 0, null);
    }
}
