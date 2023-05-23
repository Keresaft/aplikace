<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523135834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cost_category (cost_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_B06C5A741DBF857F (cost_id), INDEX IDX_B06C5A7412469DE2 (category_id), PRIMARY KEY(cost_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cost_category ADD CONSTRAINT FK_B06C5A741DBF857F FOREIGN KEY (cost_id) REFERENCES cost (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cost_category ADD CONSTRAINT FK_B06C5A7412469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cost_category DROP FOREIGN KEY FK_B06C5A741DBF857F');
        $this->addSql('ALTER TABLE cost_category DROP FOREIGN KEY FK_B06C5A7412469DE2');
        $this->addSql('DROP TABLE cost_category');
    }
}
