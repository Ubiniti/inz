<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191022143248 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_video (category_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_94F4956512469DE2 (category_id), INDEX IDX_94F4956529C1004E (video_id), PRIMARY KEY(category_id, video_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A2F98E47A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_video (playlist_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_DFDBC36F6BBD148 (playlist_id), INDEX IDX_DFDBC36F29C1004E (video_id), PRIMARY KEY(playlist_id, video_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, funds INT DEFAULT NULL, UNIQUE INDEX UNIQ_7C68921FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_video ADD CONSTRAINT FK_94F4956512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_video ADD CONSTRAINT FK_94F4956529C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE channel ADD CONSTRAINT FK_A2F98E47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE playlist_video ADD CONSTRAINT FK_DFDBC36F6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_video ADD CONSTRAINT FK_DFDBC36F29C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE video ADD channel_id INT NOT NULL, ADD price INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C72F5A1AA ON video (channel_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_video DROP FOREIGN KEY FK_94F4956512469DE2');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C72F5A1AA');
        $this->addSql('ALTER TABLE playlist_video DROP FOREIGN KEY FK_DFDBC36F6BBD148');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_video');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_video');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP INDEX IDX_7CC7DA2C72F5A1AA ON video');
        $this->addSql('ALTER TABLE video DROP channel_id, DROP price');
    }
}
