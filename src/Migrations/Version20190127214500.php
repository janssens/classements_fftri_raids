<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127214500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('INSERT INTO season (start_date,end_date) VALUES ("2018-09-01 00:00:00","2019-08-31 23:59:59");');
        $this->addSql('INSERT INTO season (start_date,end_date) VALUES ("2019-09-01 00:00:00","2020-08-31 23:59:59");');
        $this->addSql('INSERT INTO season (start_date,end_date) VALUES ("2020-09-01 00:00:00","2021-08-31 23:59:59");');
        $this->addSql('INSERT INTO season (start_date,end_date) VALUES ("2021-09-01 00:00:00","2022-08-31 23:59:59");');


        $this->addSql('CREATE VIEW view_ranking AS 
            SELECT athlete_id as id,athlete_id,sum(value) as points,team.gender as category,season.id as season_id
            FROM official_team AS team
            JOIN registration_team AS rt ON rt.team_id = team.team_id
            JOIN registration AS r ON r.id = rt.registration_id
            JOIN athlete AS a ON a.id = r.athlete_id
            JOIN race ON race.id = team.race_id
            LEFT JOIN season ON race.date > season.start_date AND race.date < season.end_date
            LEFT JOIN scale AS scale ON scale.position = (SELECT count(*)+1 FROM official_team AS subo where team.position < subo.position AND subo.race_id = team.race_id and subo.gender = team.gender)
            GROUP BY athlete_id,team.gender,season.id;');

        $this->addSql('CREATE VIEW view_official_team_ranking AS 
            SELECT team_id as id,team_id,official_team.position as overall_position,scale.value as points, official_team.race_id,
            (SELECT count(*)+1 FROM official_team AS subo 
            WHERE overall_position > subo.position 
            AND subo.race_id = official_team.race_id 
            AND subo.gender = official_team.gender) as category_position 
            FROM official_team
            LEFT JOIN scale AS scale ON scale.position = (
            SELECT count(*)+1 FROM official_team AS subo 
            WHERE official_team.position > subo.position 
            AND subo.race_id = official_team.race_id 
            AND subo.gender = official_team.gender);');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE season');

        $this->addSql('DROP VIEW view_ranking');

    }
}
