<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190125160357 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE outsider (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(100) NOT NULL, gender INT NOT NULL, INDEX IDX_530913BD296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, club_id INT DEFAULT NULL, ligue_id INT DEFAULT NULL, athlete_id INT DEFAULT NULL, number VARCHAR(50) NOT NULL, type INT NOT NULL, ask_date DATETIME NOT NULL, date DATETIME NOT NULL, INDEX IDX_62A8A7A761190A32 (club_id), INDEX IDX_62A8A7A74D7328E5 (ligue_id), INDEX IDX_62A8A7A7FE6BCB8B (athlete_id), UNIQUE INDEX registration_unique (number, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_team (registration_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_BDEB1C3833D8F43 (registration_id), INDEX IDX_BDEB1C3296CD8AE (team_id), PRIMARY KEY(registration_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lat NUMERIC(10, 6) DEFAULT NULL, lon NUMERIC(10, 6) DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE club (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE athlete (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(100) DEFAULT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, gender INT NOT NULL, dob DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, race_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT NOT NULL, INDEX IDX_C4E0A61F6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligue (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outsider ADD CONSTRAINT FK_530913BD296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A761190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A74D7328E5 FOREIGN KEY (ligue_id) REFERENCES ligue (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7FE6BCB8B FOREIGN KEY (athlete_id) REFERENCES athlete (id)');
        $this->addSql('ALTER TABLE registration_team ADD CONSTRAINT FK_BDEB1C3833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_team ADD CONSTRAINT FK_BDEB1C3296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F6E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registration_team DROP FOREIGN KEY FK_BDEB1C3833D8F43');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F6E59D40D');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A761190A32');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A7FE6BCB8B');
        $this->addSql('ALTER TABLE outsider DROP FOREIGN KEY FK_530913BD296CD8AE');
        $this->addSql('ALTER TABLE registration_team DROP FOREIGN KEY FK_BDEB1C3296CD8AE');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A74D7328E5');
        $this->addSql('DROP TABLE outsider');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE registration_team');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE athlete');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE ligue');
    }
}
