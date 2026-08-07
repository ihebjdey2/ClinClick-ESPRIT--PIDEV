<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309070202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_reclamation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_r (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, date DATE NOT NULL, INDEX IDX_B26681E12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participer (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, event_id INT DEFAULT NULL, INDEX IDX_EDBE16F8A76ED395 (user_id), INDEX IDX_EDBE16F871F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rdv (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_r DATE NOT NULL, idpatient INT NOT NULL, INDEX IDX_10C31F8612469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, categorie_reclamation_id INT DEFAULT NULL, nom VARCHAR(500) NOT NULL, email VARCHAR(60) NOT NULL, description VARCHAR(500) DEFAULT NULL, etat TINYINT(1) DEFAULT NULL, INDEX IDX_CE606404BB61C5B6 (categorie_reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, relation_reclamation_id INT NOT NULL, objet VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5FB6DEC78A79131D (relation_reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, produit VARCHAR(255) NOT NULL, quantite INT NOT NULL, quantite_disponible INT DEFAULT NULL, date_expiration DATETIME DEFAULT NULL, INDEX IDX_4B365660BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, genre VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE participer ADD CONSTRAINT FK_EDBE16F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participer ADD CONSTRAINT FK_EDBE16F871F7E88B FOREIGN KEY (event_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8612469DE2 FOREIGN KEY (category_id) REFERENCES category_r (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404BB61C5B6 FOREIGN KEY (categorie_reclamation_id) REFERENCES categorie_reclamation (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC78A79131D FOREIGN KEY (relation_reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E12469DE2');
        $this->addSql('ALTER TABLE participer DROP FOREIGN KEY FK_EDBE16F8A76ED395');
        $this->addSql('ALTER TABLE participer DROP FOREIGN KEY FK_EDBE16F871F7E88B');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F8612469DE2');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404BB61C5B6');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC78A79131D');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660BCF5E72D');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE categorie_reclamation');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_r');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE participer');
        $this->addSql('DROP TABLE rdv');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
