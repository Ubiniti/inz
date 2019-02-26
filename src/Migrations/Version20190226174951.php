<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190226174951 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment ADD parrent_hash VARCHAR(32) NOT NULL, ADD hash VARCHAR(32) NOT NULL, ADD video_hash VARCHAR(32) NOT NULL, DROP parrent_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9474526CD1B862B8 ON comment (hash)');
        $this->addSql('ALTER TABLE video CHANGE hash hash VARCHAR(32) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2CD1B862B8 ON video (hash)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_9474526CD1B862B8 ON comment');
        $this->addSql('ALTER TABLE comment ADD parrent_id INT DEFAULT NULL, DROP parrent_hash, DROP hash, DROP video_hash');
        $this->addSql('DROP INDEX UNIQ_7CC7DA2CD1B862B8 ON video');
        $this->addSql('ALTER TABLE video CHANGE hash hash VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
