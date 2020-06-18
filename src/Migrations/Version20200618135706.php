<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200618135706 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('DROP VIEW view_official_ranking');

        $this->addSql('CREATE VIEW view_official_ranking AS 
            SELECT CONCAT(LPAD(athlete_id,6,"0"),LPAD(championship.id,3,"0"),team.gender) as id,athlete_id,sum(value) as points,team.gender as category,championship.id as championship_id
            FROM view_official_team AS team
            JOIN registration_team AS rt ON rt.team_id = team.team_id
            JOIN registration AS r ON r.id = rt.registration_id
            JOIN athlete AS a ON a.id = r.athlete_id
            JOIN race ON race.id = team.race_id
            JOIN championship_race ON championship_race.race_id = race.id
            JOIN championship ON championship_race.championship_id = championship.id
            LEFT JOIN scale AS scale ON scale.position = (SELECT count(*)+1 FROM view_official_team AS subo where team.position > subo.position AND subo.race_id = team.race_id and subo.gender = team.gender)
            GROUP BY athlete_id,team.gender,championship.id;
            ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP VIEW view_official_ranking');

        $this->addSql('CREATE VIEW view_official_ranking AS 
            SELECT CONCAT(LPAD(athlete_id,6,"0"),LPAD(championship.id,3,"0")) as id,athlete_id,sum(value) as points,team.gender as category,championship.id as championship_id
            FROM view_official_team AS team
            JOIN registration_team AS rt ON rt.team_id = team.team_id
            JOIN registration AS r ON r.id = rt.registration_id
            JOIN athlete AS a ON a.id = r.athlete_id
            JOIN race ON race.id = team.race_id
            JOIN championship_race ON championship_race.race_id = race.id
            JOIN championship ON championship_race.championship_id = championship.id
            LEFT JOIN scale AS scale ON scale.position = (SELECT count(*)+1 FROM view_official_team AS subo where team.position > subo.position AND subo.race_id = team.race_id and subo.gender = team.gender)
            GROUP BY athlete_id,team.gender,championship.id;
            ');
    }
}
