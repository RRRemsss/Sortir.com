<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305095120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AC74095A876C4DDA ON activity (organizer_id)');
        $this->addSql('ALTER TABLE user ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A876C4DDA');
        $this->addSql('DROP INDEX IDX_AC74095A876C4DDA ON activity');
        $this->addSql('ALTER TABLE user DROP updated_at');
    }
}
