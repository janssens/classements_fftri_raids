<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220902195147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
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

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW view_official_team_ranking');

        $this->addSql('CREATE VIEW view_official_team_ranking AS 
            SELECT team_id as id,team_id,official_team.position as overall_position,scale.value as points, official_team.race_id,
            (SELECT count(*)+1 FROM view_official_team AS subo 
            WHERE overall_position > subo.position 
            AND subo.race_id = official_team.race_id 
            AND subo.gender = official_team.gender) as category_position 
            FROM view_official_team as official_team
            LEFT JOIN scale AS scale ON scale.position = (
            SELECT count(*)+1 FROM view_official_team AS subo 
            WHERE official_team.position > subo.position 
            AND subo.race_id = official_team.race_id 
            AND subo.gender = official_team.gender);');

    }
}
