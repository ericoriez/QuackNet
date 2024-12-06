<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205151703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE duck (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, duckname VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON duck (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_DUCKNAME ON duck (duckname)');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, parent_id INTEGER DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, photo VARCHAR(255) DEFAULT NULL, is_moderated BOOLEAN NOT NULL, CONSTRAINT FK_83D44F6FF675F31B FOREIGN KEY (author_id) REFERENCES duck (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_83D44F6F727ACA70 FOREIGN KEY (parent_id) REFERENCES quack (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_83D44F6FF675F31B ON quack (author_id)');
        $this->addSql('CREATE INDEX IDX_83D44F6F727ACA70 ON quack (parent_id)');
        $this->addSql('CREATE TABLE quack_tag (quack_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(quack_id, tag_id), CONSTRAINT FK_C7845150D3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C7845150BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C7845150D3950CA9 ON quack_tag (quack_id)');
        $this->addSql('CREATE INDEX IDX_C7845150BAD26311 ON quack_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE duck');
        $this->addSql('DROP TABLE quack');
        $this->addSql('DROP TABLE quack_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
