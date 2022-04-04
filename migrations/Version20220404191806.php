<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404191806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD employed_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63834BF9639 FOREIGN KEY (employed_id) REFERENCES employed (id)');
        $this->addSql('CREATE INDEX IDX_4C62E63834BF9639 ON contact (employed_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63834BF9639');
        $this->addSql('DROP INDEX IDX_4C62E63834BF9639 ON contact');
        $this->addSql('ALTER TABLE contact DROP employed_id');
    }
}
