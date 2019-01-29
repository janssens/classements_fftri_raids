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


    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE season');

    }
}
