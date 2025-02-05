<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205142430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event_property (event_id INT NOT NULL, property_id INT NOT NULL, PRIMARY KEY(event_id, property_id))');
        $this->addSql('CREATE INDEX IDX_CD1EF50E71F7E88B ON event_property (event_id)');
        $this->addSql('CREATE INDEX IDX_CD1EF50E549213EC ON event_property (property_id)');
        $this->addSql('CREATE TABLE property (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE event_property ADD CONSTRAINT FK_CD1EF50E71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_property ADD CONSTRAINT FK_CD1EF50E549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event_property DROP CONSTRAINT FK_CD1EF50E71F7E88B');
        $this->addSql('ALTER TABLE event_property DROP CONSTRAINT FK_CD1EF50E549213EC');
        $this->addSql('DROP TABLE event_property');
        $this->addSql('DROP TABLE property');
    }
}
