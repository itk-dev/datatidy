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

use App\DataTarget\JsonHttpDataTarget;
use App\Entity\DataTarget;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200421090948 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    }

    /**
     * Set asObject in Json data target options.
     *
     * {@inheritdoc}
     */
    public function postUp(Schema $schema): void
    {
        $tableName = $this->getTableName(DataTarget::class);
        $idName = $this->getColumnName(DataTarget::class, 'id');
        $dataTargetName = $this->getColumnName(DataTarget::class, 'dataTarget');
        $dataTargetOptionsName = $this->getColumnName(DataTarget::class, 'dataTargetOptions');
        $statement = $this->connection->prepare("SELECT $idName, $dataTargetOptionsName FROM $tableName WHERE $dataTargetName = :dataTarget");
        $statement->execute(['dataTarget' => JsonHttpDataTarget::class]);
        foreach ($statement->fetchAll() as $row) {
            $dataTargetOptions = json_decode($row[$dataTargetOptionsName], true, 512, JSON_THROW_ON_ERROR);
            $dataTargetOptions['asObject'] = false;
            $this->connection->update(
                $tableName,
                [$dataTargetOptionsName => json_encode($dataTargetOptions, JSON_THROW_ON_ERROR, 512)],
                [$idName => $row[$idName]]
            );
        }
    }

    public function down(Schema $schema): void
    {
    }

    /**
     * Remove asObject from Json data target options.
     *
     * {@inheritdoc}
     */
    public function postDown(Schema $schema): void
    {
        $tableName = $this->getTableName(DataTarget::class);
        $idName = $this->getColumnName(DataTarget::class, 'id');
        $dataTargetName = $this->getColumnName(DataTarget::class, 'dataTarget');
        $dataTargetOptionsName = $this->getColumnName(DataTarget::class, 'dataTargetOptions');
        $statement = $this->connection->prepare("SELECT $idName, $dataTargetOptionsName FROM $tableName WHERE $dataTargetName = :dataTarget");
        $statement->execute(['dataTarget' => JsonHttpDataTarget::class]);
        foreach ($statement->fetchAll() as $row) {
            $dataTargetOptions = json_decode($row[$dataTargetOptionsName], true, 512, JSON_THROW_ON_ERROR);
            unset($dataTargetOptions['asObject']);
            $this->connection->update(
                $tableName,
                [$dataTargetOptionsName => json_encode($dataTargetOptions, JSON_THROW_ON_ERROR, 512)],
                [$idName => $row[$idName]]
            );
        }
    }

    private function getTableName(string $class): string
    {
        return $this->getMetadata($class)->getTableName();
    }

    private function getColumnName(string $class, string $name): string
    {
        return $this->getMetadata($class)->getColumnName($name);
    }

    private function getMetadata(string $class): ClassMetadata
    {
        return $this->container->get('doctrine')
            ->getManager()
            ->getClassMetadata($class);
    }
}
