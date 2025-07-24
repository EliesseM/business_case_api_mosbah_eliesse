<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724091132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD reservation_utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955EEBCA076 FOREIGN KEY (reservation_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_42C84955EEBCA076 ON reservation (reservation_utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955EEBCA076');
        $this->addSql('DROP INDEX IDX_42C84955EEBCA076 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP reservation_utilisateur_id');
    }
}
