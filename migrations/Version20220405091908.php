<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405091908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, ref_mandate VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customers ADD applicant_id INT DEFAULT NULL, ADD acquirer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E2197139001 FOREIGN KEY (applicant_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E21924526F1 FOREIGN KEY (acquirer_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_62534E2197139001 ON customers (applicant_id)');
        $this->addSql('CREATE INDEX IDX_62534E21924526F1 ON customers (acquirer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E2197139001');
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E21924526F1');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP INDEX IDX_62534E2197139001 ON customers');
        $this->addSql('DROP INDEX IDX_62534E21924526F1 ON customers');
        $this->addSql('ALTER TABLE customers DROP applicant_id, DROP acquirer_id');
    }
}
