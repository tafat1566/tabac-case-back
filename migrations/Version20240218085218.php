<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218085218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement DROP CONSTRAINT fk_b1dc7a1e7dc7170a');
        $this->addSql('DROP INDEX uniq_b1dc7a1e7dc7170a');
        $this->addSql('ALTER TABLE paiement DROP vente_id');
        $this->addSql('ALTER TABLE vente DROP CONSTRAINT fk_888a2a4cf347efb');
        $this->addSql('DROP INDEX idx_888a2a4cf347efb');
        $this->addSql('ALTER TABLE vente DROP produit_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE paiement ADD vente_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT fk_b1dc7a1e7dc7170a FOREIGN KEY (vente_id) REFERENCES vente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_b1dc7a1e7dc7170a ON paiement (vente_id)');
        $this->addSql('ALTER TABLE vente ADD produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT fk_888a2a4cf347efb FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_888a2a4cf347efb ON vente (produit_id)');
    }
}
