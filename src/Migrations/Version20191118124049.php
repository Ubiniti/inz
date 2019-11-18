<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191118124049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE video_rate (id INT AUTO_INCREMENT NOT NULL, video_id INT NOT NULL, author VARCHAR(180) NOT NULL, rate TINYINT(1) NOT NULL, INDEX IDX_D87B62E529C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, title VARCHAR(255) NOT NULL, is_public TINYINT(1) NOT NULL, INDEX IDX_D782112D72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_video (playlist_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_DFDBC36F6BBD148 (playlist_id), INDEX IDX_DFDBC36F29C1004E (video_id), PRIMARY KEY(playlist_id, video_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_category (playlist_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_FB91DFAC6BBD148 (playlist_id), INDEX IDX_FB91DFAC12469DE2 (category_id), PRIMARY KEY(playlist_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(30) NOT NULL, join_date DATE NOT NULL, country VARCHAR(30) NOT NULL, birthday DATE NOT NULL, avatar VARCHAR(32) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_category (user_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_E6C1FDC1A76ED395 (user_id), INDEX IDX_E6C1FDC112469DE2 (category_id), PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, video_id INT NOT NULL, parent_id INT DEFAULT NULL, author_id INT NOT NULL, contents LONGTEXT NOT NULL, author_username VARCHAR(180) NOT NULL, added DATETIME NOT NULL, INDEX IDX_9474526C29C1004E (video_id), INDEX IDX_9474526C727ACA70 (parent_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, hash VARCHAR(32) NOT NULL, title VARCHAR(255) NOT NULL, author_username VARCHAR(180) NOT NULL, uploaded DATETIME NOT NULL, views INT NOT NULL, description LONGTEXT DEFAULT NULL, duration INT NOT NULL, price INT DEFAULT 0, is_public TINYINT(1) DEFAULT NULL, allows_ads TINYINT(1) DEFAULT NULL, has_demo TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_7CC7DA2CD1B862B8 (hash), INDEX IDX_7CC7DA2C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video_category (video_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_AECE2B7D29C1004E (video_id), INDEX IDX_AECE2B7D12469DE2 (category_id), PRIMARY KEY(video_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video_user (video_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8A048B9529C1004E (video_id), INDEX IDX_8A048B95A76ED395 (user_id), PRIMARY KEY(video_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_rate (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, comment_id INT NOT NULL, rate TINYINT(1) NOT NULL, INDEX IDX_F9DA9DD5F675F31B (author_id), INDEX IDX_F9DA9DD5F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A2F98E47A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, funds INT DEFAULT NULL, UNIQUE INDEX UNIQ_7C68921FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advertisement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, views INT NOT NULL, is_paid_off TINYINT(1) NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_C95F6AEEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE video_rate ADD CONSTRAINT FK_D87B62E529C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112D72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('ALTER TABLE playlist_video ADD CONSTRAINT FK_DFDBC36F6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_video ADD CONSTRAINT FK_DFDBC36F29C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_category ADD CONSTRAINT FK_FB91DFAC6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_category ADD CONSTRAINT FK_FB91DFAC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C29C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('ALTER TABLE video_category ADD CONSTRAINT FK_AECE2B7D29C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_category ADD CONSTRAINT FK_AECE2B7D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_user ADD CONSTRAINT FK_8A048B9529C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_user ADD CONSTRAINT FK_8A048B95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_rate ADD CONSTRAINT FK_F9DA9DD5F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment_rate ADD CONSTRAINT FK_F9DA9DD5F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE channel ADD CONSTRAINT FK_A2F98E47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE advertisement ADD CONSTRAINT FK_C95F6AEEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE playlist_video DROP FOREIGN KEY FK_DFDBC36F6BBD148');
        $this->addSql('ALTER TABLE playlist_category DROP FOREIGN KEY FK_FB91DFAC6BBD148');
        $this->addSql('ALTER TABLE user_category DROP FOREIGN KEY FK_E6C1FDC1A76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE video_user DROP FOREIGN KEY FK_8A048B95A76ED395');
        $this->addSql('ALTER TABLE comment_rate DROP FOREIGN KEY FK_F9DA9DD5F675F31B');
        $this->addSql('ALTER TABLE channel DROP FOREIGN KEY FK_A2F98E47A76ED395');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921FA76ED395');
        $this->addSql('ALTER TABLE advertisement DROP FOREIGN KEY FK_C95F6AEEA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment_rate DROP FOREIGN KEY FK_F9DA9DD5F8697D13');
        $this->addSql('ALTER TABLE video_rate DROP FOREIGN KEY FK_D87B62E529C1004E');
        $this->addSql('ALTER TABLE playlist_video DROP FOREIGN KEY FK_DFDBC36F29C1004E');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C29C1004E');
        $this->addSql('ALTER TABLE video_category DROP FOREIGN KEY FK_AECE2B7D29C1004E');
        $this->addSql('ALTER TABLE video_user DROP FOREIGN KEY FK_8A048B9529C1004E');
        $this->addSql('ALTER TABLE playlist_category DROP FOREIGN KEY FK_FB91DFAC12469DE2');
        $this->addSql('ALTER TABLE user_category DROP FOREIGN KEY FK_E6C1FDC112469DE2');
        $this->addSql('ALTER TABLE video_category DROP FOREIGN KEY FK_AECE2B7D12469DE2');
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112D72F5A1AA');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C72F5A1AA');
        $this->addSql('DROP TABLE video_rate');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_video');
        $this->addSql('DROP TABLE playlist_category');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE video');
        $this->addSql('DROP TABLE video_category');
        $this->addSql('DROP TABLE video_user');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment_rate');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE advertisement');
    }
}
