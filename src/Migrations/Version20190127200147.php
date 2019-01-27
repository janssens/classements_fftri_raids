<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127200147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE VIEW view_official_team 
            AS SELECT t.id as id,t.id as team_id,t.position,race.id as race_id,race.athletes_per_team as number_of_athlete,IF (SUM(a.gender)=athletes_per_team*2,"F",IF (SUM(a.gender)=athletes_per_team,"M","X")) as gender 
            FROM registration_team as rt
             JOIN team AS t ON t.id = rt.team_id
             JOIN race ON t.race_id = race.id
             JOIN registration AS r ON r.id = rt.registration_id
             JOIN athlete AS a ON a.id = r.athlete_id
            GROUP BY rt.team_id
            HAVING COUNT(*) = number_of_athlete;');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP VIEW view_official_team');
    }
}
