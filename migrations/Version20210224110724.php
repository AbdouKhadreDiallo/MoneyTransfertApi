<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224110724 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agence DROP FOREIGN KEY FK_64C19AA9F2C56620');
        $this->addSql('DROP INDEX UNIQ_64C19AA9F2C56620 ON agence');
        $this->addSql('ALTER TABLE agence DROP compte_id');
        $this->addSql('ALTER TABLE comptes ADD agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comptes ADD CONSTRAINT FK_56735801D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56735801D725330D ON comptes (agence_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agence ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agence ADD CONSTRAINT FK_64C19AA9F2C56620 FOREIGN KEY (compte_id) REFERENCES comptes (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19AA9F2C56620 ON agence (compte_id)');
        $this->addSql('ALTER TABLE comptes DROP FOREIGN KEY FK_56735801D725330D');
        $this->addSql('DROP INDEX UNIQ_56735801D725330D ON comptes');
        $this->addSql('ALTER TABLE comptes DROP agence_id');
    }
}
