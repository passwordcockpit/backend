<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add last_password_change_date column to password table.
 */
final class Version20260922120000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add last_password_modification_date column to password table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE password ADD last_password_modification_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE password DROP last_password_modification_date');
    }
}
