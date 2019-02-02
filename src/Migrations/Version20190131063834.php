<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190131063834 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE championship (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_EBADDE6A4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE championship_race (championship_id INT NOT NULL, race_id INT NOT NULL, INDEX IDX_FE698EE694DDBCE9 (championship_id), INDEX IDX_FE698EE66E59D40D (race_id), PRIMARY KEY(championship_id, race_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE championship ADD CONSTRAINT FK_EBADDE6A4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE championship_race ADD CONSTRAINT FK_FE698EE694DDBCE9 FOREIGN KEY (championship_id) REFERENCES championship (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE championship_race ADD CONSTRAINT FK_FE698EE66E59D40D FOREIGN KEY (race_id) REFERENCES race (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE championship_race DROP FOREIGN KEY FK_FE698EE694DDBCE9');
        $this->addSql('DROP TABLE championship');
        $this->addSql('DROP TABLE championship_race');
    }
}
