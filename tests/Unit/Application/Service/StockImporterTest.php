<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Contract\StocksReaderInterface;
use App\Application\Service\Data\StockData;
use App\Application\Service\StockImporter;
use App\Application\Service\Validator\StockDataValidatorInterface;
use App\Application\Service\Validator\ValidationException;
use App\Domain\Repository\StockRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;
use function PHPUnit\Framework\once;

class StockImporterTest extends TestCase
{
    private StocksReaderInterface&MockObject $stocksReaderMock;
    private StockRepositoryInterface&MockObject $stockRepositoryMock;
    private StockDataValidatorInterface&MockObject $validatorMock;
    private StockImporter $subject;

    protected function setUp(): void
    {
        $this->stocksReaderMock = $this->createMock(StocksReaderInterface::class);
        $this->stockRepositoryMock = $this->createMock(StockRepositoryInterface::class);
        $this->validatorMock = $this->createMock(StockDataValidatorInterface::class);

        $this->subject = new StockImporter(
            $this->stocksReaderMock,
            $this->stockRepositoryMock,
            $this->validatorMock
        );
    }

    public function testSuccessfulDryRunImport()
    {
        $resource = fopen('php://temp', 'r+');
        $this->stocksReaderMock->expects(once())->method('readFromStream')->willReturn($this->getValidInput());
        $this->stockRepositoryMock->expects(self::never())->method('bulkSave');
        $this->stockRepositoryMock
            ->expects(self::exactly(5))
            ->method('findByCode')
            ->willReturn(null);

        $this->validatorMock->expects(self::exactly(5))->method('validate');


        $report = $this->subject->import($resource, false);
        self::assertEquals(2, $report->failedImportCount);
        self::assertEquals(3, $report->successfulImportCount);
    }

    public function testDryRunImportWithFailures()
    {
        $resource = fopen('php://temp', 'r+');
        $this->stocksReaderMock->expects(once())->method('readFromStream')->willReturn($this->getInvalidInput());
        $this->stockRepositoryMock->expects(self::never())->method('bulkSave');
        $this->stockRepositoryMock
            ->expects(self::never())
            ->method('findByCode');

        $this->validatorMock->expects(self::exactly(4))->method('validate')->willThrowException(new ValidationException());


        $report = $this->subject->import($resource, false);
        self::assertEquals(4, $report->failedImportCount);
        self::assertEquals(0, $report->successfulImportCount);
    }

    private function getValidInput(): array
    {
        return [
            new StockData('P0001', 'TV', '32” Tv', '9', '399.99', null),
            new StockData('P0002', 'Cd Player', 'Nice CD player', '11', '50.12', 'yes'),
            new StockData('P0003', 'VCR', 'Top notch VCR', '12', '39.33', 'yes'),
            new StockData('P0004', 'Bluray Player', 'Watch it in HD', '9', '4', null),
            new StockData('P0006', 'Bluray Player', 'Watch it in HD', '1', '10001', null),
        ];
    }

    private function getInvalidInput(): array
    {
        return [
            new StockData('P0007', '24” Monitor', 'Awesome', null, '35.99', null),
            new StockData('P0011', 'Misc Cables', 'error in export', null, null, null),
            new StockData('P0015', 'Bluray Player', 'Excellent picture', '32', '$4.33', null),
            new StockData('P0017', 'CPU', 'Processing power', ' ideal for multimedia', '4', '4.22',),
        ];
    }

}
