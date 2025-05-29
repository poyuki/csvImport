<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Model\Stock;
use App\Domain\Repository\StockRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Iterator;

class StockRepository extends ServiceEntityRepository implements StockRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function findById(int $id): ?Stock
    {
        return $this->find($id);
    }

    public function findByCode(string $code): ?Stock
    {
        return $this->findOneBy(['productCode' => $code]);
    }

    public function save(Stock $stock): void
    {
        $this->getEntityManager()->persist($stock);
        $this->getEntityManager()->flush();
    }

    public function bulkSave(iterable $stocks): void
    {
        foreach ($stocks as $stock) {
            $this->getEntityManager()->persist($stock);
        }
        $this->getEntityManager()->flush();
    }
}
