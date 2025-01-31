<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131062706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE visit (id SERIAL NOT NULL, event_id INT NOT NULL, visitor_id INT NOT NULL, visited BOOLEAN DEFAULT true NOT NULL, link VARCHAR(255) DEFAULT NULL, rating SMALLINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_437EE93971F7E88B ON visit (event_id)');
        $this->addSql('CREATE INDEX IDX_437EE93970BEE6D ON visit (visitor_id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE93971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE93970BEE6D FOREIGN KEY (visitor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE visit DROP CONSTRAINT FK_437EE93971F7E88B');
        $this->addSql('ALTER TABLE visit DROP CONSTRAINT FK_437EE93970BEE6D');
        $this->addSql('DROP TABLE visit');
    }
}
