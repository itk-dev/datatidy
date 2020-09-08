<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\ReferenceManager;

use App\DataTransformer\MergeFlowsDataTransformer;
use App\Entity\DataFlow;
use App\Entity\DataTransform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataFlowReferenceManager implements ReferenceManagerInterface
{
    /** @var EntityManagerInterface */
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
        return $entity instanceof DataFlow;
    }

    /**
     * Get a list of messages telling why a given data flow cannot be deleted.
     *
     * @return Message[]
     */
    public function getDeleteMessages($dataFlow): array
    {
        $messages = [];

        if ($dataFlow instanceof DataFlow) {
            if (!$this->security->isGranted('edit', $dataFlow)) {
                $messages[] = new Message($this->translator->trans('You are not allowed to delete this data flow'));
            }

            $transforms = $this->entityManager->getRepository(DataTransform::class)
                ->findBy(['transformer' => MergeFlowsDataTransformer::class]);
            /** @var DataTransform $transform */
            foreach ($transforms as $transform) {
                $flowId = $transform->getTransformerOptions()['dataFlow'] ?? null;
                if ($dataFlow->getId() === $flowId) {
                    $messages[] = new Message($this->translator->trans(
                        'Cannot delete data flow %name% because it is used in the data flow %other_name%.',
                        [
                            '%name%' => $dataFlow->getName(),
                            '%other_name%' => $transform->getDataFlow()->getName(),
                        ]
                    ));
                }
            }
        }

        return $messages;
    }

    public function delete($dataFlow, bool $flush = true)
    {
        $this->entityManager->remove($dataFlow);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
