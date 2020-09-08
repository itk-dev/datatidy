<?php

declare(strict_types=1);

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200907082630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Deletes all file data targets';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM data_target WHERE data_target = :data_target', ['data_target' => 'App\DataTarget\FileDataTarget']);
    }

    public function down(Schema $schema): void
    {
    }
}
