<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260806234000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Aligne les longueurs métier et rend obligatoires les champs de réclamation validés par le formulaire.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categorie CHANGE libelle libelle VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE categorie_reclamation CHANGE nom nom VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE category CHANGE nom nom VARCHAR(80) NOT NULL, CHANGE libelle libelle VARCHAR(160) NOT NULL');
        $this->addSql('ALTER TABLE category_r CHANGE nom nom VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE evenement CHANGE titre titre VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404BB61C5B6');
        $this->addSql('ALTER TABLE reclamation CHANGE categorie_reclamation_id categorie_reclamation_id INT NOT NULL, CHANGE nom nom VARCHAR(120) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE description description VARCHAR(500) NOT NULL, CHANGE etat etat TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404BB61C5B6 FOREIGN KEY (categorie_reclamation_id) REFERENCES categorie_reclamation (id)');
        $this->addSql('ALTER TABLE user CHANGE nom nom VARCHAR(80) NOT NULL, CHANGE prenom prenom VARCHAR(80) NOT NULL, CHANGE genre genre VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categorie CHANGE libelle libelle VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE categorie_reclamation CHANGE nom nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE libelle libelle VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category_r CHANGE nom nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement CHANGE titre titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404BB61C5B6');
        $this->addSql('ALTER TABLE reclamation CHANGE categorie_reclamation_id categorie_reclamation_id INT DEFAULT NULL, CHANGE nom nom VARCHAR(500) NOT NULL, CHANGE email email VARCHAR(60) NOT NULL, CHANGE description description VARCHAR(500) DEFAULT NULL, CHANGE etat etat TINYINT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404BB61C5B6 FOREIGN KEY (categorie_reclamation_id) REFERENCES categorie_reclamation (id)');
        $this->addSql('ALTER TABLE user CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE genre genre VARCHAR(255) NOT NULL');
    }
}
