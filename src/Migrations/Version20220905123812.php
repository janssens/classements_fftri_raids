<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220905123812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE VIEW view_club_ranking AS 
            SELECT CONCAT(LPAD(club_id,3,"0"),LPAD(championship_id,3,"0")) as id,
            club_id, 
            championship_id,
            SUM(points) as points
              FROM view_official_ranking
              GROUP BY CONCAT(club_id,\'_\',championship_id);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW view_club_ranking');

    }
}
