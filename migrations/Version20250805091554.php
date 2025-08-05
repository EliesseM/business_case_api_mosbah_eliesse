<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250805091554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD slug VARCHAR(255) DEFAULT NULL, ADD is_published TINYINT(1) NOT NULL, ADD image_url VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F65593E5989D9B62 ON annonce (slug)');
        $this->addSql('ALTER TABLE commentaire CHANGE date_publication date_publication VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE indisponibilite CHANGE date_debut date_debut VARCHAR(255) NOT NULL, CHANGE date_fin date_fin VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F65593E5989D9B62 ON annonce');
        $this->addSql('ALTER TABLE annonce DROP slug, DROP is_published, DROP image_url, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE reservation CHANGE date_debut date_debut DATETIME NOT NULL, CHANGE date_fin date_fin DATETIME NOT NULL');
        $this->addSql('ALTER TABLE commentaire CHANGE date_publication date_publication DATETIME NOT NULL');
        $this->addSql('ALTER TABLE indisponibilite CHANGE date_debut date_debut DATETIME NOT NULL, CHANGE date_fin date_fin DATETIME NOT NULL');
    }
}
