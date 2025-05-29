<?php

declare(strict_types=1);

namespace App\Application\Service\Data;

use Symfony\Component\Validator\Constraints as Assert;


final readonly class StockData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    public ?string $productCode;
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $productName;
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $productDescription;
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\d+$/')]
    public ?string $stock;
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\d+(\.\d+)?$/')]
    public ?string $cost;
    public ?string $discontinued;

    public function __construct(
        ?string $productCode,
        ?string $productName,
        ?string $productDescription,
        ?string $stock,
        ?string $cost,
        ?string $discontinued,
    ) {
        $this->productCode = $productCode;
        $this->productName = $productName;
        $this->productDescription = $productDescription;
        $this->stock = $stock;
        $this->cost = $cost;
        $this->discontinued = $discontinued;
    }
}
