<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190514192350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registration_planned_team (registration_id INT NOT NULL, planned_team_id INT NOT NULL, INDEX IDX_D9461346833D8F43 (registration_id), INDEX IDX_D946134669443F9A (planned_team_id), PRIMARY KEY(registration_id, planned_team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request_planned_team (registration_id INT NOT NULL, planned_team_id INT NOT NULL, INDEX IDX_6492925A833D8F43 (registration_id), INDEX IDX_6492925A69443F9A (planned_team_id), PRIMARY KEY(registration_id, planned_team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planned_team (id INT AUTO_INCREMENT NOT NULL, race_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_7FB8CB826E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registration_planned_team ADD CONSTRAINT FK_D9461346833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_planned_team ADD CONSTRAINT FK_D946134669443F9A FOREIGN KEY (planned_team_id) REFERENCES planned_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE request_planned_team ADD CONSTRAINT FK_6492925A833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE request_planned_team ADD CONSTRAINT FK_6492925A69443F9A FOREIGN KEY (planned_team_id) REFERENCES planned_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planned_team ADD CONSTRAINT FK_7FB8CB826E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registration_planned_team DROP FOREIGN KEY FK_D946134669443F9A');
        $this->addSql('ALTER TABLE request_planned_team DROP FOREIGN KEY FK_6492925A69443F9A');
        $this->addSql('DROP TABLE registration_planned_team');
        $this->addSql('DROP TABLE request_planned_team');
        $this->addSql('DROP TABLE planned_team');
    }
}
