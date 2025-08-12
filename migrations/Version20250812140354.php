<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812140354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, annonce_logement_id INT NOT NULL, annonce_utilisateur_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prix_journee DOUBLE PRECISION NOT NULL, nb_places INT NOT NULL, mixte TINYINT(1) NOT NULL, slug VARCHAR(255) DEFAULT NULL, is_published TINYINT(1) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_F65593E5989D9B62 (slug), INDEX IDX_F65593E52C045F1A (annonce_logement_id), INDEX IDX_F65593E53E7A8ED0 (annonce_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annonce_service (annonce_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_1BF200B28805AB2F (annonce_id), INDEX IDX_1BF200B2ED5CA9E6 (service_id), PRIMARY KEY(annonce_id, service_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, commentaire_utilisateur_id INT DEFAULT NULL, commentaire_reservation_id INT DEFAULT NULL, note INT NOT NULL, commentaire LONGTEXT NOT NULL, date_publication DATETIME NOT NULL, INDEX IDX_67F068BC19F40945 (commentaire_utilisateur_id), INDEX IDX_67F068BC5A4E7FED (commentaire_reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, logements VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, logement_image_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_C53D045FB8BAC2A5 (logement_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indisponibilite (id INT AUTO_INCREMENT NOT NULL, annonce_indisponibilite_id INT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_8717036FE6185741 (annonce_indisponibilite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logement (id INT AUTO_INCREMENT NOT NULL, logement_utilisateur_id INT DEFAULT NULL, numero_rue VARCHAR(255) NOT NULL, nom_rue VARCHAR(255) NOT NULL, complement_adresse1 VARCHAR(255) DEFAULT NULL, complement_adresse2 VARCHAR(255) DEFAULT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, superficie DOUBLE PRECISION NOT NULL, INDEX IDX_F0FD44577343F8FB (logement_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logement_equipement (logement_id INT NOT NULL, equipement_id INT NOT NULL, INDEX IDX_85F9697158ABF955 (logement_id), INDEX IDX_85F96971806F0F5C (equipement_id), PRIMARY KEY(logement_id, equipement_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, message_receiver_id INT DEFAULT NULL, message_sender_id INT DEFAULT NULL, contenu LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6BD307FAD2CB34F (message_receiver_id), INDEX IDX_B6BD307F9C9DB5AB (message_sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, reservation_annonce_id INT DEFAULT NULL, reservation_utilisateur_id INT DEFAULT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, status VARCHAR(255) NOT NULL, prix_total DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_42C84955E8AF629B (reservation_annonce_id), INDEX IDX_42C84955EEBCA076 (reservation_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, username VARCHAR(255) NOT NULL, genre VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, date_naissance DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_verified TINYINT(1) NOT NULL, profil_picture VARCHAR(255) DEFAULT NULL, billing_address VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3F85E0677 (username), UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E52C045F1A FOREIGN KEY (annonce_logement_id) REFERENCES logement (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E53E7A8ED0 FOREIGN KEY (annonce_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B28805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC19F40945 FOREIGN KEY (commentaire_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC5A4E7FED FOREIGN KEY (commentaire_reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FB8BAC2A5 FOREIGN KEY (logement_image_id) REFERENCES logement (id)');
        $this->addSql('ALTER TABLE indisponibilite ADD CONSTRAINT FK_8717036FE6185741 FOREIGN KEY (annonce_indisponibilite_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE logement ADD CONSTRAINT FK_F0FD44577343F8FB FOREIGN KEY (logement_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE logement_equipement ADD CONSTRAINT FK_85F9697158ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE logement_equipement ADD CONSTRAINT FK_85F96971806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FAD2CB34F FOREIGN KEY (message_receiver_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9C9DB5AB FOREIGN KEY (message_sender_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E8AF629B FOREIGN KEY (reservation_annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955EEBCA076 FOREIGN KEY (reservation_utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E52C045F1A');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E53E7A8ED0');
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B28805AB2F');
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B2ED5CA9E6');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC19F40945');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC5A4E7FED');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FB8BAC2A5');
        $this->addSql('ALTER TABLE indisponibilite DROP FOREIGN KEY FK_8717036FE6185741');
        $this->addSql('ALTER TABLE logement DROP FOREIGN KEY FK_F0FD44577343F8FB');
        $this->addSql('ALTER TABLE logement_equipement DROP FOREIGN KEY FK_85F9697158ABF955');
        $this->addSql('ALTER TABLE logement_equipement DROP FOREIGN KEY FK_85F96971806F0F5C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FAD2CB34F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9C9DB5AB');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E8AF629B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955EEBCA076');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE annonce_service');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE indisponibilite');
        $this->addSql('DROP TABLE logement');
        $this->addSql('DROP TABLE logement_equipement');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE utilisateur');
    }
}
