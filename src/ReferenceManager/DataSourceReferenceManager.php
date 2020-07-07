<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\ReferenceManager;

use App\Entity\DataFlow;
use App\Entity\DataSource;
use App\Entity\User;
use App\Repository\DataFlowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataSourceReferenceManager implements ReferenceManagerInterface
{
    /** @var DataFlowRepository */
    private $entityManager;

    /** @var Security */
    private $security;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, Security $security, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function supports($entity): bool
    {
        return $entity instanceof DataSource;
    }

    /**
     * Get a list of messages telling why a given user cannot be deleted.
     *
     * @return Message[]
     */
    public function getDeleteMessages($dataFlow): array
    {
        $messages = [];

        if ($dataFlow instanceof DataSource) {
            if (!$this->security->isGranted('edit', $dataFlow)) {
                $messages[] = new Message($this->translator->trans('You are not allowed to delete this data source'));
            }

            $dataFlows = $this->entityManager->getRepository(DataFlow::class)->findBy(['dataSource' => $dataFlow]);
            if (!empty($dataFlows)) {
                $messages[] = new Message($this->translator->trans('This data source is used in data flows'));
            }
        }

        return $messages;
    }

    public function delete($dataSource, bool $flush = true)
    {
        $this->entityManager->remove($dataSource);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
