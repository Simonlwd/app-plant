<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260513182913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE observation (id INT AUTO_INCREMENT NOT NULL, observed_at DATETIME NOT NULL, notes LONGTEXT DEFAULT NULL, location_name VARCHAR(150) DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, suspected_name VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_C576DBE0A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE observation ADD CONSTRAINT FK_C576DBE0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE observation DROP FOREIGN KEY FK_C576DBE0A76ED395');
        $this->addSql('DROP TABLE observation');
    }
}
