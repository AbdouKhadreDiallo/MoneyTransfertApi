<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224220849 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, cni VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, user_autor_id INT DEFAULT NULL, user_author_depot_id INT DEFAULT NULL, user_author_retrait_id INT DEFAULT NULL, admin_system_author_depot_id INT DEFAULT NULL, admin_system_author_retrait_id INT DEFAULT NULL, client_author_depot_id INT DEFAULT NULL, client_author_retrait_id INT DEFAULT NULL, montant INT NOT NULL, date_depot DATE NOT NULL, date_retrait DATE NOT NULL, code_transmission VARCHAR(255) NOT NULL, frais INT NOT NULL, frais_depot INT NOT NULL, frais_retrait INT NOT NULL, frais_etat INT NOT NULL, frais_system INT NOT NULL, INDEX IDX_723705D1F2C56620 (compte_id), INDEX IDX_723705D1F459BFBE (user_autor_id), INDEX IDX_723705D1785C92E4 (user_author_depot_id), INDEX IDX_723705D1C4EE82EA (user_author_retrait_id), INDEX IDX_723705D1145CD9B7 (admin_system_author_depot_id), INDEX IDX_723705D1B4767865 (admin_system_author_retrait_id), INDEX IDX_723705D117A6A398 (client_author_depot_id), INDEX IDX_723705D116BADBD0 (client_author_retrait_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F2C56620 FOREIGN KEY (compte_id) REFERENCES comptes (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F459BFBE FOREIGN KEY (user_autor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1785C92E4 FOREIGN KEY (user_author_depot_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1C4EE82EA FOREIGN KEY (user_author_retrait_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1145CD9B7 FOREIGN KEY (admin_system_author_depot_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B4767865 FOREIGN KEY (admin_system_author_retrait_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D117A6A398 FOREIGN KEY (client_author_depot_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D116BADBD0 FOREIGN KEY (client_author_retrait_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D117A6A398');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D116BADBD0');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE transaction');
    }
}
