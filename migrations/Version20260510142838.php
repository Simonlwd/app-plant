<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260510142838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB030D72A2A71819 ON plant (latin_name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB030D72989D9B62 ON plant (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_AB030D72A2A71819 ON plant');
        $this->addSql('DROP INDEX UNIQ_AB030D72989D9B62 ON plant');
    }
}
