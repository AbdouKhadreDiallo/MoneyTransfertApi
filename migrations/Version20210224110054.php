<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224110054 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comptes ADD creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comptes ADD CONSTRAINT FK_5673580161220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_5673580161220EA6 ON comptes (creator_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DCED588B');
        $this->addSql('DROP INDEX IDX_8D93D649DCED588B ON user');
        $this->addSql('ALTER TABLE user DROP comptes_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comptes DROP FOREIGN KEY FK_5673580161220EA6');
        $this->addSql('DROP INDEX IDX_5673580161220EA6 ON comptes');
        $this->addSql('ALTER TABLE comptes DROP creator_id');
        $this->addSql('ALTER TABLE `user` ADD comptes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649DCED588B FOREIGN KEY (comptes_id) REFERENCES comptes (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649DCED588B ON `user` (comptes_id)');
    }
}
