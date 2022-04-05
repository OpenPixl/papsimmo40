<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405084945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE complements (id INT AUTO_INCREMENT NOT NULL, denomination_id INT DEFAULT NULL, house_type_id INT DEFAULT NULL, apartment_type_id INT DEFAULT NULL, land_type_id INT DEFAULT NULL, trade_type_id INT DEFAULT NULL, building_equipment_id INT DEFAULT NULL, house_equipment_id INT DEFAULT NULL, other_option_id INT DEFAULT NULL, banner VARCHAR(25) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, disponibility VARCHAR(100) NOT NULL, disponibility_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', construction_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', property_tax NUMERIC(10, 2) DEFAULT NULL, orientation VARCHAR(100) DEFAULT NULL, house_state VARCHAR(100) DEFAULT NULL, level INT DEFAULT NULL, jointness INT DEFAULT NULL, washroom INT DEFAULT NULL, bathroom INT DEFAULT NULL, wc INT DEFAULT NULL, terrace INT DEFAULT NULL, balcony INT DEFAULT NULL, sanitation VARCHAR(100) DEFAULT NULL, is_furnished TINYINT(1) NOT NULL, energy VARCHAR(100) DEFAULT NULL, INDEX IDX_3A429FA0E9293F06 (denomination_id), INDEX IDX_3A429FA0519B0A8E (house_type_id), INDEX IDX_3A429FA0497A6219 (apartment_type_id), INDEX IDX_3A429FA0DD50F8F2 (land_type_id), INDEX IDX_3A429FA036CCD465 (trade_type_id), INDEX IDX_3A429FA0CA86D771 (building_equipment_id), INDEX IDX_3A429FA09CD63F81 (house_equipment_id), INDEX IDX_3A429FA0793B94DA (other_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, ref_employed_id INT DEFAULT NULL, options_id INT DEFAULT NULL, ref VARCHAR(100) DEFAULT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, annonce LONGTEXT DEFAULT NULL, ppiece INT DEFAULT NULL, room INT DEFAULT NULL, is_home TINYINT(1) NOT NULL, is_apartment TINYINT(1) NOT NULL, is_land TINYINT(1) NOT NULL, is_other TINYINT(1) NOT NULL, other_description VARCHAR(255) NOT NULL, surface_land NUMERIC(10, 2) DEFAULT NULL, surface_home NUMERIC(10, 2) NOT NULL, dpe_at DATE DEFAULT NULL, diag_dpe INT NOT NULL, diag_gpe VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, complement VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(10) NOT NULL, city VARCHAR(255) NOT NULL, notary_estimate NUMERIC(10, 2) DEFAULT NULL, applicant_estimate NUMERIC(10, 2) DEFAULT NULL, cadaster_zone VARCHAR(255) DEFAULT NULL, cadaster_num INT DEFAULT NULL, cadaster_surface INT DEFAULT NULL, cadaster_cariez INT DEFAULT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8BF21CDECEB66EB5 (ref_employed_id), UNIQUE INDEX UNIQ_8BF21CDE3ADB05F1 (options_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA0E9293F06 FOREIGN KEY (denomination_id) REFERENCES denomination (id)');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA0519B0A8E FOREIGN KEY (house_type_id) REFERENCES house_type (id)');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA0497A6219 FOREIGN KEY (apartment_type_id) REFERENCES apartment_type (id)');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA0DD50F8F2 FOREIGN KEY (land_type_id) REFERENCES land_type (id)');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA036CCD465 FOREIGN KEY (trade_type_id) REFERENCES trade_type (id)');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA0CA86D771 FOREIGN KEY (building_equipment_id) REFERENCES building_equipment (id)');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA09CD63F81 FOREIGN KEY (house_equipment_id) REFERENCES house_equipment (id)');
        $this->addSql('ALTER TABLE complements ADD CONSTRAINT FK_3A429FA0793B94DA FOREIGN KEY (other_option_id) REFERENCES other_option (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDECEB66EB5 FOREIGN KEY (ref_employed_id) REFERENCES employed (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE3ADB05F1 FOREIGN KEY (options_id) REFERENCES complements (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE3ADB05F1');
        $this->addSql('DROP TABLE complements');
        $this->addSql('DROP TABLE property');
    }
}
