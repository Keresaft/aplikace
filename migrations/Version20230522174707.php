<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522174707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_cost (category_id INT NOT NULL, cost_id INT NOT NULL, INDEX IDX_93BDEEBB12469DE2 (category_id), INDEX IDX_93BDEEBB1DBF857F (cost_id), PRIMARY KEY(category_id, cost_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_cost ADD CONSTRAINT FK_93BDEEBB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_cost ADD CONSTRAINT FK_93BDEEBB1DBF857F FOREIGN KEY (cost_id) REFERENCES cost (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_cost DROP FOREIGN KEY FK_93BDEEBB12469DE2');
        $this->addSql('ALTER TABLE category_cost DROP FOREIGN KEY FK_93BDEEBB1DBF857F');
        $this->addSql('DROP TABLE category_cost');
    }
}
