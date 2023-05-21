<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521094946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE details (id INT AUTO_INCREMENT NOT NULL, ico VARCHAR(15) NOT NULL, dico VARCHAR(50) DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(50) NOT NULL, phone_number VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer ADD details_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09BB1A0722 FOREIGN KEY (details_id) REFERENCES details (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09BB1A0722 ON customer (details_id)');
        $this->addSql('ALTER TABLE user ADD details_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BB1A0722 FOREIGN KEY (details_id) REFERENCES details (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BB1A0722 ON user (details_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09BB1A0722');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BB1A0722');
        $this->addSql('DROP TABLE details');
        $this->addSql('DROP INDEX UNIQ_81398E09BB1A0722 ON customer');
        $this->addSql('ALTER TABLE customer DROP details_id');
        $this->addSql('DROP INDEX UNIQ_8D93D649BB1A0722 ON user');
        $this->addSql('ALTER TABLE user DROP details_id');
    }
}
