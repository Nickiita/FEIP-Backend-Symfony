<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624130559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, phone VARCHAR(16) NOT NULL, comment LONGTEXT DEFAULT NULL, house_id_id INT NOT NULL, INDEX IDX_E00CEDDEA4A739AF (house_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE house (id INT AUTO_INCREMENT NOT NULL, quantity_single_beds INT NOT NULL, quantity_double_beds INT NOT NULL, sea_distance INT NOT NULL, is_available TINYINT(1) NOT NULL, price_per_night DOUBLE PRECISION NOT NULL, quantity_parking_spaces INT NOT NULL, has_shower TINYINT(1) NOT NULL, has_kitchen TINYINT(1) NOT NULL, has_terrace TINYINT(1) NOT NULL, has_ac TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA4A739AF FOREIGN KEY (house_id_id) REFERENCES house (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA4A739AF
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE house
        SQL);
    }
}
