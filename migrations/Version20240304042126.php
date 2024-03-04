<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304042126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993D725330D');
        $this->addSql('DROP INDEX fk_60349993d725330d ON contrat');
        $this->addSql('CREATE INDEX IDX_60349993D725330D ON contrat (agence_id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993D725330D');
        $this->addSql('DROP INDEX idx_60349993d725330d ON contrat');
        $this->addSql('CREATE INDEX FK_60349993D725330D ON contrat (agence_id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
    }
}
