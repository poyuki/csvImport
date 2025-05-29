<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;


use App\Application\Service\Data\StockData;
use App\Application\Service\Validator\StockDataValidatorInterface;
use App\Application\Service\Validator\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class StockDataValidator implements StockDataValidatorInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(StockData $stockData): void
    {
        $validationResult = $this->validator->validate($stockData);
        if ($validationResult->count() !== 0) {
            throw new ValidationException(iterator_to_array($validationResult), $stockData);
        }
    }
}
