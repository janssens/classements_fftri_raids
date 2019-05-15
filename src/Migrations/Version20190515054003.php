<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190515054003 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registration_planned_team DROP FOREIGN KEY FK_D946134669443F9A');
        $this->addSql('ALTER TABLE registration_planned_team DROP FOREIGN KEY FK_D9461346833D8F43');
        $this->addSql('ALTER TABLE registration_planned_team DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE registration_planned_team ADD CONSTRAINT FK_D946134669443F9A FOREIGN KEY (planned_team_id) REFERENCES planned_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_planned_team ADD CONSTRAINT FK_D9461346833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_planned_team ADD PRIMARY KEY (planned_team_id, registration_id)');
        $this->addSql('ALTER TABLE request_planned_team DROP FOREIGN KEY FK_6492925A69443F9A');
        $this->addSql('ALTER TABLE request_planned_team DROP FOREIGN KEY FK_6492925A833D8F43');
        $this->addSql('ALTER TABLE request_planned_team DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE request_planned_team ADD CONSTRAINT FK_6492925A69443F9A FOREIGN KEY (planned_team_id) REFERENCES planned_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE request_planned_team ADD CONSTRAINT FK_6492925A833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE request_planned_team ADD PRIMARY KEY (planned_team_id, registration_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registration_planned_team DROP FOREIGN KEY FK_D946134669443F9A');
        $this->addSql('ALTER TABLE registration_planned_team DROP FOREIGN KEY FK_D9461346833D8F43');
        $this->addSql('ALTER TABLE registration_planned_team DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE registration_planned_team ADD CONSTRAINT FK_D946134669443F9A FOREIGN KEY (planned_team_id) REFERENCES registration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_planned_team ADD CONSTRAINT FK_D9461346833D8F43 FOREIGN KEY (registration_id) REFERENCES planned_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_planned_team ADD PRIMARY KEY (registration_id, planned_team_id)');
        $this->addSql('ALTER TABLE request_planned_team DROP FOREIGN KEY FK_6492925A69443F9A');
        $this->addSql('ALTER TABLE request_planned_team DROP FOREIGN KEY FK_6492925A833D8F43');
        $this->addSql('ALTER TABLE request_planned_team DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE request_planned_team ADD CONSTRAINT FK_6492925A69443F9A FOREIGN KEY (planned_team_id) REFERENCES registration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE request_planned_team ADD CONSTRAINT FK_6492925A833D8F43 FOREIGN KEY (registration_id) REFERENCES planned_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE request_planned_team ADD PRIMARY KEY (registration_id, planned_team_id)');
    }
}
