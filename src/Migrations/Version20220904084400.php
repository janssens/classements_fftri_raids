<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220904084400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW view_official_team_ranking');

        $this->addSql('CREATE VIEW view_official_team_ranking AS 
            SELECT CONCAT(LPAD(athlete_id,6,"0"),LPAD(championship.id,3,"0"),team.gender) as id,
       athlete_id,
       r.club_id as club_id,
       if (season.start_date > "2021-08-31 23:59:59",
           (SELECT GREATEST(LEAST(150,count(*)) - SUM(CASE WHEN (team.position > other_team.position) THEN 1 ELSE 0 END), 1) FROM view_team AS other_team where other_team.race_id = team.race_id and other_team.gender = team.gender),sum(value)) as points,
       team.gender as category,championship.id as championship_id
FROM view_official_team AS team
         JOIN registration_team AS rt ON rt.team_id = team.team_id
         JOIN registration AS r ON r.id = rt.registration_id
         JOIN athlete AS a ON a.id = r.athlete_id
         JOIN race ON race.id = team.race_id
         JOIN championship_race ON championship_race.race_id = race.id
         JOIN championship ON championship_race.championship_id = championship.id
         JOIN season ON championship.season_id = season.id
         LEFT JOIN scale AS scale ON scale.position = (SELECT count(*)+1 FROM view_official_team AS subo where team.position > subo.position AND subo.race_id = team.race_id and subo.gender = team.gender)
GROUP BY athlete_id,team.gender,championship.id;
');


    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW view_official_team_ranking');

        $this->addSql('CREATE VIEW view_official_team_ranking AS 
            SELECT team_id as id,team_id,official_team.position as overall_position,
       if (r.date > "2021-08-31 23:59:59",
           (SELECT GREATEST(LEAST(150,count(*)) - SUM(CASE WHEN (official_team.position > other_team.position) THEN 1 ELSE 0 END), 1) FROM view_team AS other_team where other_team.race_id = official_team.race_id and other_team.gender = official_team.gender),scale.value) as points,
       official_team.race_id,
       (SELECT count(*)+1 FROM view_official_team AS subo
        WHERE overall_position > subo.position
          AND subo.race_id = official_team.race_id
          AND subo.gender = official_team.gender) as category_position
FROM view_official_team as official_team
    LEFT JOIN race r on r.id = official_team.race_id
         LEFT JOIN scale AS scale ON scale.position = (
    SELECT count(*)+1 FROM view_official_team AS subo
    WHERE official_team.position > subo.position
      AND subo.race_id = official_team.race_id
      AND subo.gender = official_team.gender);');

    }
}
