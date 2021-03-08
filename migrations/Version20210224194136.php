<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224194136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot ADD admin_system_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBC39622A97 FOREIGN KEY (admin_system_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_47948BBC39622A97 ON depot (admin_system_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBC39622A97');
        $this->addSql('DROP INDEX IDX_47948BBC39622A97 ON depot');
        $this->addSql('ALTER TABLE depot DROP admin_system_id');
    }
}
