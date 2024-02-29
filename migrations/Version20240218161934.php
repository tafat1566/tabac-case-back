<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218161934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiement_produit (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, paiement_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_349F5CD62A4C4478 ON paiement_produit (paiement_id)');
        $this->addSql('CREATE INDEX IDX_349F5CD6F347EFB ON paiement_produit (produit_id)');
        $this->addSql('ALTER TABLE paiement_produit ADD CONSTRAINT FK_349F5CD62A4C4478 FOREIGN KEY (paiement_id) REFERENCES paiement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE paiement_produit ADD CONSTRAINT FK_349F5CD6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE paiement_produit DROP CONSTRAINT FK_349F5CD62A4C4478');
        $this->addSql('ALTER TABLE paiement_produit DROP CONSTRAINT FK_349F5CD6F347EFB');
        $this->addSql('DROP TABLE paiement_produit');
    }
}
