<?php

declare(strict_types=1);

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104142053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE data_flow (id INT AUTO_INCREMENT NOT NULL, data_source_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, ttl INT NOT NULL, last_run_at DATETIME DEFAULT NULL, INDEX IDX_E98F1D921A935C57 (data_source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_data_transform (id INT AUTO_INCREMENT NOT NULL, data_flow_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, position INT NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8CFD5E6A1ABD5BEA (data_flow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_flow ADD CONSTRAINT FK_E98F1D921A935C57 FOREIGN KEY (data_source_id) REFERENCES data_source (id)');
        $this->addSql('ALTER TABLE abstract_data_transform ADD CONSTRAINT FK_8CFD5E6A1ABD5BEA FOREIGN KEY (data_flow_id) REFERENCES data_flow (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE abstract_data_transform DROP FOREIGN KEY FK_8CFD5E6A1ABD5BEA');
        $this->addSql('DROP TABLE data_flow');
        $this->addSql('DROP TABLE abstract_data_transform');
    }
}
