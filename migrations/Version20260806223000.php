<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260806223000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les consultations et prescriptions privées liées aux rendez-vous.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, diagnosis VARCHAR(255) NOT NULL, clinical_notes LONGTEXT NOT NULL, consulted_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_CONSULTATION_APPOINTMENT (appointment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE prescription (id INT AUTO_INCREMENT NOT NULL, consultation_id INT NOT NULL, medication VARCHAR(160) NOT NULL, dosage VARCHAR(120) NOT NULL, frequency VARCHAR(160) NOT NULL, instructions VARCHAR(500) DEFAULT NULL, start_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', end_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_PRESCRIPTION_CONSULTATION (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_CONSULTATION_APPOINTMENT FOREIGN KEY (appointment_id) REFERENCES rdv (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_PRESCRIPTION_CONSULTATION FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_PRESCRIPTION_CONSULTATION');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_CONSULTATION_APPOINTMENT');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('DROP TABLE consultation');
    }
}
