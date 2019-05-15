<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190514210956 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'link user and athlete';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user ADD athlete_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A6479FE6BCB8B FOREIGN KEY (athlete_id) REFERENCES athlete (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479FE6BCB8B ON fos_user (athlete_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A6479FE6BCB8B');
        $this->addSql('DROP INDEX UNIQ_957A6479FE6BCB8B ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP athlete_id');
    }
}
