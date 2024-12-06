<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241206102305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quack ADD COLUMN is_comment BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, author_id, parent_id, content, created_at, photo, is_moderated FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, parent_id INTEGER DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, photo VARCHAR(255) DEFAULT NULL, is_moderated BOOLEAN NOT NULL, CONSTRAINT FK_83D44F6FF675F31B FOREIGN KEY (author_id) REFERENCES duck (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_83D44F6F727ACA70 FOREIGN KEY (parent_id) REFERENCES quack (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO quack (id, author_id, parent_id, content, created_at, photo, is_moderated) SELECT id, author_id, parent_id, content, created_at, photo, is_moderated FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
        $this->addSql('CREATE INDEX IDX_83D44F6FF675F31B ON quack (author_id)');
        $this->addSql('CREATE INDEX IDX_83D44F6F727ACA70 ON quack (parent_id)');
    }
}
