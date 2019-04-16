<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416065702 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('DROP VIEW view_ranking');

        $this->addSql('CREATE VIEW view_official_ranking AS 
            SELECT CONCAT(athlete_id,team.gender) as id,athlete_id,sum(value) as points,team.gender as category,championship.id as championship_id
            FROM view_official_team AS team
            JOIN registration_team AS rt ON rt.team_id = team.team_id
            JOIN registration AS r ON r.id = rt.registration_id
            JOIN athlete AS a ON a.id = r.athlete_id
            JOIN race ON race.id = team.race_id
            JOIN championship_race ON championship_race.race_id = race.id
            JOIN championship ON championship_race.championship_id = championship.id
            LEFT JOIN scale AS scale ON scale.position = (SELECT count(*)+1 FROM view_official_team AS subo where team.position > subo.position AND subo.race_id = team.race_id and subo.gender = team.gender)
            WHERE r.start_date < championship.registration_due_date
            GROUP BY athlete_id,team.gender,championship.id;
            ');

        $this->addSql('CREATE VIEW view_racer AS 
            SELECT a.id as id,0 as outsider,a.id as parent_id,t.id  as team_id,a.firstname,a.lastname
            FROM team as t
            JOIN registration_team AS rt ON (t.id = rt.team_id)
            JOIN registration AS r ON (r.id = rt.registration_id)
            JOIN athlete AS a ON (a.id = r.athlete_id)
            UNION ALL
            SELECT o.uid as id,1 as outsider,o.id as parent_id,o.team_id  as team_id,o.firstname,o.lastname
            FROM outsider as o;
            ');

        $this->addSql('CREATE VIEW view_team AS 
            SELECT t.id AS id,
            t.id AS team_id,
            t.position AS position,
            race.id AS race_id,
            race.athletes_per_team AS number_of_athlete,
            if((sum(IFNULL(a.gender,0)+ IFNULL(o.gender,0)) = (race.athletes_per_team * 2)),\'F\',
                if((sum(IFNULL(a.gender,0)+ IFNULL(o.gender,0)) = race.athletes_per_team),\'M\',
                    \'X\')) AS gender
            FROM team AS t
            JOIN race ON (t.race_id = race.id)
            lEFT JOIN outsider AS o ON (o.team_id = t.id)
            LEFT JOIN registration_team AS rt ON (t.id = rt.team_id)
            LEFT JOIN registration AS r ON (r.id = rt.registration_id)
            LEFT JOIN athlete AS a ON (a.id = r.athlete_id)
            GROUP BY t.id;');

        $this->addSql('CREATE VIEW view_team_ranking AS 
            SELECT team.team_id AS id,
            team.team_id AS team_id,
            team.position AS overall_position,
            scale.value AS points,
            team.race_id AS race_id,
            (SELECT (count(0) + 1) 
                FROM view_team subo 
                WHERE ((overall_position > subo.position) 
                AND (subo.race_id = team.race_id) 
                AND (subo.gender = team.gender))) AS category_position 
                FROM (view_team AS team 
                    LEFT JOIN `scale` ON (scale.position = 
                        (SELECT (count(0) + 1) FROM view_team AS subo 
                            WHERE ((team.position > subo.position) 
                            AND (subo.race_id = team.race_id) 
                            AND (subo.gender = team.gender)
                        )
                    )
                )
            )');

        $this->addSql('CREATE VIEW view_ranking AS 
            SELECT concat(racer.id,team.gender) AS id,
            racer.id AS racer_id,
            racer.outsider AS outsider,
            sum(scale.value) AS points,
            team.gender AS category,
            championship_race.championship_id AS championship_id 
            FROM (view_team AS team 
                JOIN view_racer AS racer ON racer.team_id = team.team_id
                JOIN race ON race.id = team.race_id
                JOIN championship_race ON championship_race.race_id = race.id
                LEFT JOIN scale
                ON scale.position = 
                    (SELECT (count(0) + 1) 
                        FROM view_team AS subo 
                        WHERE ((team.position > subo.position) 
                        AND (subo.race_id = team.race_id) 
                        AND (subo.gender = team.gender))
                    )
            )
            GROUP BY racer.id,team.gender,championship_race.championship_id');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP VIEW view_ranking');

        $this->addSql('DROP VIEW view_official_ranking');

        $this->addSql('DROP VIEW view_racer');

        $this->addSql('DROP VIEW view_team');

        $this->addSql('DROP VIEW view_team_ranking');

        $this->addSql('CREATE VIEW view_ranking AS 
            SELECT CONCAT(athlete_id,team.gender) as id,athlete_id,sum(value) as points,team.gender as category,championship.id as championship_id
            FROM view_official_team AS team
            JOIN registration_team AS rt ON rt.team_id = team.team_id
            JOIN registration AS r ON r.id = rt.registration_id
            JOIN athlete AS a ON a.id = r.athlete_id
            JOIN race ON race.id = team.race_id
            JOIN championship_race ON championship_race.race_id = race.id
            JOIN championship ON championship_race.championship_id = championship.id
            LEFT JOIN scale AS scale ON scale.position = (SELECT count(*)+1 FROM view_official_team AS subo where team.position > subo.position AND subo.race_id = team.race_id and subo.gender = team.gender)
            WHERE r.start_date < championship.registration_due_date
            GROUP BY athlete_id,team.gender,championship.id;
            ');
    }
}
