<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127131900 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE club CHANGE name name VARCHAR(100) NOT NULL, CHANGE slug slug VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8EE3872989D9B62 ON club (slug)');
        $this->addSql('ALTER TABLE ligue CHANGE name name VARCHAR(100) NOT NULL, CHANGE slug slug VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FEC61019989D9B62 ON ligue (slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_B8EE3872989D9B62 ON club');
        $this->addSql('ALTER TABLE club CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE slug slug VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_FEC61019989D9B62 ON ligue');
        $this->addSql('ALTER TABLE ligue CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE slug slug VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
