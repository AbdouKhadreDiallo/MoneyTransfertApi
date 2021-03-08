<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225101413 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F459BFBE');
        $this->addSql('DROP INDEX IDX_723705D1F459BFBE ON transaction');
        $this->addSql('ALTER TABLE transaction ADD is_finished TINYINT(1) NOT NULL, DROP user_autor_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD user_autor_id INT DEFAULT NULL, DROP is_finished');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F459BFBE FOREIGN KEY (user_autor_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_723705D1F459BFBE ON transaction (user_autor_id)');
    }
}
