<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260806210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le planning clinique, les relations patient/médecin, les statuts et les contraintes d’intégrité.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE rdv ADD patient_id INT DEFAULT NULL, ADD doctor_id INT DEFAULT NULL, ADD scheduled_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD duration_minutes SMALLINT NOT NULL DEFAULT 30, ADD status VARCHAR(20) NOT NULL DEFAULT 'pending', ADD notes VARCHAR(500) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE idpatient idpatient INT DEFAULT NULL");
        $this->addSql("UPDATE rdv SET scheduled_at = TIMESTAMP(date_r, '09:00:00'), created_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE scheduled_at IS NULL");
        $this->addSql('UPDATE rdv appointment INNER JOIN user patient ON patient.id = appointment.idpatient SET appointment.patient_id = patient.id WHERE appointment.patient_id IS NULL');
        $this->addSql("ALTER TABLE rdv CHANGE scheduled_at scheduled_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_RDV_PATIENT FOREIGN KEY (patient_id) REFERENCES user (id), ADD CONSTRAINT FK_RDV_DOCTOR FOREIGN KEY (doctor_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX idx_rdv_doctor_schedule ON rdv (doctor_id, scheduled_at, status)');
        $this->addSql('CREATE INDEX idx_rdv_patient_schedule ON rdv (patient_id, scheduled_at)');

        $this->addSql('CREATE TABLE doctor_availability (id INT AUTO_INCREMENT NOT NULL, doctor_id INT NOT NULL, day_of_week SMALLINT NOT NULL, start_time TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\', end_time TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\', is_active TINYINT(1) NOT NULL, INDEX idx_availability_doctor_day (doctor_id, day_of_week, is_active), UNIQUE INDEX uniq_doctor_availability_slot (doctor_id, day_of_week, start_time, end_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doctor_availability ADD CONSTRAINT FK_AVAILABILITY_DOCTOR FOREIGN KEY (doctor_id) REFERENCES user (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE participer CHANGE user_id user_id INT NOT NULL, CHANGE event_id event_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_participation_user_event ON participer (user_id, event_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_stock_category_name ON categorie (libelle)');
        $this->addSql('CREATE UNIQUE INDEX uniq_complaint_category_name ON categorie_reclamation (nom)');
        $this->addSql('CREATE UNIQUE INDEX uniq_appointment_category_name ON category_r (nom)');
        $this->addSql('CREATE UNIQUE INDEX uniq_event_category_name ON category (nom)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE doctor_availability DROP FOREIGN KEY FK_AVAILABILITY_DOCTOR');
        $this->addSql('DROP TABLE doctor_availability');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_RDV_PATIENT, DROP FOREIGN KEY FK_RDV_DOCTOR');
        $this->addSql('DROP INDEX idx_rdv_doctor_schedule ON rdv');
        $this->addSql('DROP INDEX idx_rdv_patient_schedule ON rdv');
        $this->addSql('ALTER TABLE rdv DROP patient_id, DROP doctor_id, DROP scheduled_at, DROP duration_minutes, DROP status, DROP notes, DROP created_at, DROP updated_at, CHANGE idpatient idpatient INT NOT NULL');
        $this->addSql('DROP INDEX uniq_participation_user_event ON participer');
        $this->addSql('ALTER TABLE participer CHANGE user_id user_id INT DEFAULT NULL, CHANGE event_id event_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX uniq_stock_category_name ON categorie');
        $this->addSql('DROP INDEX uniq_complaint_category_name ON categorie_reclamation');
        $this->addSql('DROP INDEX uniq_appointment_category_name ON category_r');
        $this->addSql('DROP INDEX uniq_event_category_name ON category');
    }
}
