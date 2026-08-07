<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260806223100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Aligne les noms des index médicaux avec les métadonnées Doctrine.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_964685A6E5B533F9 ON consultation (appointment_id)');
        $this->addSql('DROP INDEX UNIQ_CONSULTATION_APPOINTMENT ON consultation');
        $this->addSql('CREATE INDEX IDX_1FBFB8D962FF6CDF ON prescription (consultation_id)');
        $this->addSql('DROP INDEX IDX_PRESCRIPTION_CONSULTATION ON prescription');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CONSULTATION_APPOINTMENT ON consultation (appointment_id)');
        $this->addSql('DROP INDEX UNIQ_964685A6E5B533F9 ON consultation');
        $this->addSql('CREATE INDEX IDX_PRESCRIPTION_CONSULTATION ON prescription (consultation_id)');
        $this->addSql('DROP INDEX IDX_1FBFB8D962FF6CDF ON prescription');
    }
}
