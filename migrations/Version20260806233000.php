<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260806233000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Aligne les types avec DBAL 4 et retire la table Messenger vide et inutilisée.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE consultation CHANGE consulted_at consulted_at DATETIME NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE doctor_availability CHANGE start_time start_time TIME NOT NULL, CHANGE end_time end_time TIME NOT NULL');
        $this->addSql('ALTER TABLE prescription CHANGE start_date start_date DATE NOT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE rdv CHANGE scheduled_at scheduled_at DATETIME NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("ALTER TABLE consultation CHANGE consulted_at consulted_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("ALTER TABLE doctor_availability CHANGE start_time start_time TIME NOT NULL COMMENT '(DC2Type:time_immutable)', CHANGE end_time end_time TIME NOT NULL COMMENT '(DC2Type:time_immutable)'");
        $this->addSql("ALTER TABLE prescription CHANGE start_date start_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', CHANGE end_date end_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("ALTER TABLE rdv CHANGE scheduled_at scheduled_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT '(DC2Type:json)'");
    }
}
