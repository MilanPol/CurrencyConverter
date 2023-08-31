<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230831074011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE default_currency (id BIGINT AUTO_INCREMENT NOT NULL, code VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_rate_currency (id BIGINT AUTO_INCREMENT NOT NULL, default_currency_id BIGINT DEFAULT NULL, rate DOUBLE PRECISION NOT NULL, inverse_rate DOUBLE PRECISION NOT NULL, updated_on DATETIME NOT NULL, target_currency_code VARCHAR(255) DEFAULT NULL, INDEX IDX_1FD403DECD792C0 (default_currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exchange_rate_currency ADD CONSTRAINT FK_1FD403DECD792C0 FOREIGN KEY (default_currency_id) REFERENCES default_currency (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exchange_rate_currency DROP FOREIGN KEY FK_1FD403DECD792C0');
        $this->addSql('DROP TABLE default_currency');
        $this->addSql('DROP TABLE exchange_rate_currency');
    }
}
