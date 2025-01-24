<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124082141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event (id SERIAL NOT NULL, cover_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, starts_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ends_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, academic_hours DOUBLE PRECISION DEFAULT NULL, remote_link VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7989D9B62 ON event (slug)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7922726E9 ON event (cover_id)');
        $this->addSql('COMMENT ON COLUMN event.starts_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.ends_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE media_link (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, type SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, uuid UUID NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, father_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7922726E9 FOREIGN KEY (cover_id) REFERENCES media_link (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA7922726E9');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE media_link');
        $this->addSql('DROP TABLE "user"');
    }
}
