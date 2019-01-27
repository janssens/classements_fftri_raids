<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127170958 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE scale (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, value INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');


        $this->addSql('INSERT INTO scale (position,value) VALUES (1,40);');
        $this->addSql('INSERT INTO scale (position,value) VALUES (2,35);');
        $this->addSql('INSERT INTO scale (position,value) VALUES (3,30);');
        $points = 27;
        for ($i=4;$points>0;$points--){
            $this->addSql('INSERT INTO scale (position,value) VALUES ('.$i++.','.$points.');');
        }

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE scale');
    }
}
