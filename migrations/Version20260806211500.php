<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260806211500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Retire les valeurs par défaut techniques après le remplissage des rendez-vous existants.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rdv CHANGE duration_minutes duration_minutes SMALLINT NOT NULL, CHANGE status status VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE rdv CHANGE duration_minutes duration_minutes SMALLINT NOT NULL DEFAULT 30, CHANGE status status VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }
}
