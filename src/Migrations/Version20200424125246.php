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
final class Version20200424125246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_flow DROP FOREIGN KEY FK_E98F1D92896DBBDE');
        $this->addSql('ALTER TABLE data_flow DROP FOREIGN KEY FK_E98F1D92B03A8386');
        $this->addSql('ALTER TABLE data_flow_collaborator DROP FOREIGN KEY FK_F35E17A3A76ED395');
        $this->addSql('ALTER TABLE data_source DROP FOREIGN KEY FK_3F744E6A896DBBDE');
        $this->addSql('ALTER TABLE data_source DROP FOREIGN KEY FK_3F744E6AB03A8386');
        $this->addSql('ALTER TABLE data_transform DROP FOREIGN KEY FK_F81DC19B896DBBDE');
        $this->addSql('ALTER TABLE data_transform DROP FOREIGN KEY FK_F81DC19BB03A8386');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Migrate (some) data from old user table to new user table.
        $this->addSql('INSERT INTO user (id, email, roles, password, enabled) SELECT id, email, roles, password, enabled FROM fos_user');

        $this->addSql('DROP TABLE fos_user');
        $this->addSql('ALTER TABLE data_flow CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE updated_by_id updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE data_flow ADD CONSTRAINT FK_E98F1D92896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_flow ADD CONSTRAINT FK_E98F1D92B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_flow_collaborator CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE data_flow_collaborator ADD CONSTRAINT FK_F35E17A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_transform CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE updated_by_id updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE data_transform ADD CONSTRAINT FK_F81DC19B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_transform ADD CONSTRAINT FK_F81DC19BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_source CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE updated_by_id updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE data_source ADD CONSTRAINT FK_3F744E6A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE data_source ADD CONSTRAINT FK_3F744E6AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_flow DROP FOREIGN KEY FK_E98F1D92B03A8386');
        $this->addSql('ALTER TABLE data_flow DROP FOREIGN KEY FK_E98F1D92896DBBDE');
        $this->addSql('ALTER TABLE data_flow_collaborator DROP FOREIGN KEY FK_F35E17A3A76ED395');
        $this->addSql('ALTER TABLE data_transform DROP FOREIGN KEY FK_F81DC19BB03A8386');
        $this->addSql('ALTER TABLE data_transform DROP FOREIGN KEY FK_F81DC19B896DBBDE');
        $this->addSql('ALTER TABLE data_source DROP FOREIGN KEY FK_3F744E6AB03A8386');
        $this->addSql('ALTER TABLE data_source DROP FOREIGN KEY FK_3F744E6A896DBBDE');
        $this->addSql('CREATE TABLE fos_user (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', username VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, username_canonical VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email_canonical VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE data_flow CHANGE created_by_id created_by_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', CHANGE updated_by_id updated_by_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_flow ADD CONSTRAINT FK_E98F1D92B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE data_flow ADD CONSTRAINT FK_E98F1D92896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE data_flow_collaborator CHANGE user_id user_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_flow_collaborator ADD CONSTRAINT FK_F35E17A3A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_source CHANGE created_by_id created_by_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', CHANGE updated_by_id updated_by_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_source ADD CONSTRAINT FK_3F744E6AB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE data_source ADD CONSTRAINT FK_3F744E6A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE data_transform CHANGE created_by_id created_by_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', CHANGE updated_by_id updated_by_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE data_transform ADD CONSTRAINT FK_F81DC19BB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE data_transform ADD CONSTRAINT FK_F81DC19B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user (id)');
    }
}
