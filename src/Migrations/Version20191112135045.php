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
final class Version20191112135045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE data_flow (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_source_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, ttl INT NOT NULL, last_run_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E98F1D921A935C57 (data_source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_source (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) NOT NULL, ttl INT NOT NULL, last_read_at DATETIME DEFAULT NULL, data_source VARCHAR(255) NOT NULL, data_source_options LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_transform (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_flow_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, transformer VARCHAR(255) NOT NULL, transformer_options LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', position INT NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F81DC19B1ABD5BEA (data_flow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_target (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', data_flow_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', description LONGTEXT NOT NULL, data_target VARCHAR(255) NOT NULL, data_target_options LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_26911EE51ABD5BEA (data_flow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('ALTER TABLE data_flow ADD CONSTRAINT FK_E98F1D921A935C57 FOREIGN KEY (data_source_id) REFERENCES data_source (id)');
        $this->addSql('ALTER TABLE data_transform ADD CONSTRAINT FK_F81DC19B1ABD5BEA FOREIGN KEY (data_flow_id) REFERENCES data_flow (id)');
        $this->addSql('ALTER TABLE data_target ADD CONSTRAINT FK_26911EE51ABD5BEA FOREIGN KEY (data_flow_id) REFERENCES data_flow (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_transform DROP FOREIGN KEY FK_F81DC19B1ABD5BEA');
        $this->addSql('ALTER TABLE data_target DROP FOREIGN KEY FK_26911EE51ABD5BEA');
        $this->addSql('ALTER TABLE data_flow DROP FOREIGN KEY FK_E98F1D921A935C57');
        $this->addSql('DROP TABLE data_flow');
        $this->addSql('DROP TABLE data_source');
        $this->addSql('DROP TABLE data_transform');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE data_target');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}
