<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225074533 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D116BADBD0');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D117A6A398');
        $this->addSql('DROP INDEX IDX_723705D117A6A398 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D116BADBD0 ON transaction');
        $this->addSql('ALTER TABLE transaction ADD sender_id INT DEFAULT NULL, ADD receiver_id INT DEFAULT NULL, DROP client_author_depot_id, DROP client_author_retrait_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F624B39D FOREIGN KEY (sender_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_723705D1F624B39D ON transaction (sender_id)');
        $this->addSql('CREATE INDEX IDX_723705D1CD53EDB6 ON transaction (receiver_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F624B39D');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1CD53EDB6');
        $this->addSql('DROP INDEX IDX_723705D1F624B39D ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1CD53EDB6 ON transaction');
        $this->addSql('ALTER TABLE transaction ADD client_author_depot_id INT DEFAULT NULL, ADD client_author_retrait_id INT DEFAULT NULL, DROP sender_id, DROP receiver_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D116BADBD0 FOREIGN KEY (client_author_retrait_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D117A6A398 FOREIGN KEY (client_author_depot_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_723705D117A6A398 ON transaction (client_author_depot_id)');
        $this->addSql('CREATE INDEX IDX_723705D116BADBD0 ON transaction (client_author_retrait_id)');
    }
}
