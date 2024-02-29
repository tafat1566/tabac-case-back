<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207125157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement ADD vente_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E7DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1DC7A1E7DC7170A ON paiement (vente_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE paiement DROP CONSTRAINT FK_B1DC7A1E7DC7170A');
        $this->addSql('DROP INDEX UNIQ_B1DC7A1E7DC7170A');
        $this->addSql('ALTER TABLE paiement DROP vente_id');
    }
}
