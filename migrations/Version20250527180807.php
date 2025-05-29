<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250527180807 extends AbstractMigration
{
    private const TABLE_NAME = 'tblProductData';

    public function getDescription(): string
    {
        return 'Add Stock and Cost fields';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->addColumn('intStock', 'integer', ['unsigned' => true, 'notnull' => true]);
        $table->addColumn(
            'decCost',
            'decimal',
            ['precision' => 10, 'scale' => 2, 'notnull' => true]
        );
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->dropColumn('intStock');
        $table->dropColumn('decCost');
    }
}
