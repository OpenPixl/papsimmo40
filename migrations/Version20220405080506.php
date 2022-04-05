<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405080506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apartment_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building_equipment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, employed_id INT DEFAULT NULL, customers_id INT NOT NULL, home VARCHAR(14) DEFAULT NULL, desk VARCHAR(14) DEFAULT NULL, gsm VARCHAR(14) NOT NULL, fax VARCHAR(14) DEFAULT NULL, other_email VARCHAR(100) DEFAULT NULL, facebook VARCHAR(100) DEFAULT NULL, instagram VARCHAR(100) DEFAULT NULL, linkedin VARCHAR(100) DEFAULT NULL, INDEX IDX_4C62E63834BF9639 (employed_id), INDEX IDX_4C62E638C3568B40 (customers_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, code VARCHAR(6) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, customer_type_id INT DEFAULT NULL, ref_employed_id INT NOT NULL, ref_customer VARCHAR(25) DEFAULT NULL, first_name VARCHAR(80) NOT NULL, last_name VARCHAR(80) NOT NULL, slug VARCHAR(125) NOT NULL, adress VARCHAR(255) DEFAULT NULL, complement VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(10) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_62534E21D991282D (customer_type_id), INDEX IDX_62534E21CEB66EB5 (ref_employed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE denomination (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employed (id INT AUTO_INCREMENT NOT NULL, referent_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(50) DEFAULT NULL, last_name VARCHAR(50) DEFAULT NULL, slug VARCHAR(80) NOT NULL, sector VARCHAR(80) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_2A984537E7927C74 (email), INDEX IDX_2A98453735E47E35 (referent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE house_equipment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE house_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE land_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mandate_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) NOT NULL, slug VARCHAR(80) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE other_option (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trade_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63834BF9639 FOREIGN KEY (employed_id) REFERENCES employed (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638C3568B40 FOREIGN KEY (customers_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E21D991282D FOREIGN KEY (customer_type_id) REFERENCES customer_type (id)');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E21CEB66EB5 FOREIGN KEY (ref_employed_id) REFERENCES employed (id)');
        $this->addSql('ALTER TABLE employed ADD CONSTRAINT FK_2A98453735E47E35 FOREIGN KEY (referent_id) REFERENCES employed (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E21D991282D');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638C3568B40');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63834BF9639');
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E21CEB66EB5');
        $this->addSql('ALTER TABLE employed DROP FOREIGN KEY FK_2A98453735E47E35');
        $this->addSql('DROP TABLE apartment_type');
        $this->addSql('DROP TABLE building_equipment');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE customer_type');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE denomination');
        $this->addSql('DROP TABLE employed');
        $this->addSql('DROP TABLE house_equipment');
        $this->addSql('DROP TABLE house_type');
        $this->addSql('DROP TABLE land_type');
        $this->addSql('DROP TABLE mandate_type');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE other_option');
        $this->addSql('DROP TABLE trade_type');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
