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

use App\DataTransformer\ReplaceValuesDataTransformer;
use App\Entity\DataTransform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207135254 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return 'Convert transforms from replace value to replace values';
    }

    public function up(Schema $schema): void
    {
    }

    public function postUp(Schema $schema): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $transforms = $em->getRepository(DataTransform::class)
            ->findBy(['transformer' => 'App\DataTransformer\ReplaceValueDataTransformer']);

        foreach ($transforms as $transform) {
            $options = $transform->getTransformerOptions();
            $options['replacements'] = [
                [
                    'from' => $options['search'],
                    'to' => $options['replace'],
                ],
            ];
            unset($options['search'], $options['replace']);
            $transform
                ->setTransformer(ReplaceValuesDataTransformer::class)
                ->setTransformerOptions($options);
            $em->persist($transform);
        }
        $em->flush();
    }

    public function down(Schema $schema): void
    {
    }

    public function postDown(Schema $schema): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $transforms = $em->getRepository(DataTransform::class)
            ->findBy(['transformer' => ReplaceValuesDataTransformer::class]);

        foreach ($transforms as $transform) {
            $options = $transform->getTransformerOptions();
            $replacement = reset($options['replacements']);
            $options['search'] = $replacement['from'] ?? null;
            $options['replace'] = $replacement['to'] ?? null;
            unset($options['replacements']);
            $transform
                ->setTransformer('App\DataTransformer\ReplaceValueDataTransformer')
                ->setTransformerOptions($options);
            $em->persist($transform);
        }
        $em->flush();
    }
}
