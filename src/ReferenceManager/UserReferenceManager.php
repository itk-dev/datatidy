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
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserReferenceManager implements ReferenceManagerInterface
{
    /** @var UserManagerInterface */
    private $userManager;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Security */
    private $security;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $entityManager, Security $security, TranslatorInterface $translator)
    {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function supports($entity): bool
    {
        return $entity instanceof User;
    }

    /**
     * Get a list of messages telling why a given user cannot be deleted.
     *
     * @return Message[]
     */
    public function getDeleteMessages($user): array
    {
        $messages = [];

        if ($user instanceof User) {
            if ($user === $this->security->getUser()) {
                $messages[] = new Message($this->translator->trans('You cannot delete yourself'));
            } else {
                if (!$this->security->isGranted('ROLE_USER_ADMIN')) {
                    $messages[] = new Message($this->translator->trans('You are not allowed to delete user: %user%', ['%user%' => $user->getUsername()]));
                }
                $dataSources = $this->entityManager->getRepository(DataSource::class)->findBy(['createdBy' => $user]);
                if (!empty($dataSources)) {
                    $messages[] = new Message($this->translator->trans('User has data sources'));
                }

                $dataFlows = $this->entityManager->getRepository(DataFlow::class)->findBy(['createdBy' => $user]);
                if (!empty($dataFlows)) {
                    $messages[] = new Message($this->translator->trans('User has data flows'));
                }
            }
        }

        return $messages;
    }

    public function delete($user, bool $flush = true)
    {
        // Remove user as collaborator
        /** @var DataFlow[] $dataFlows */
        $dataFlows = $this->entityManager->getRepository(DataFlow::class)
            ->createQueryBuilder('f')
            ->where(':user MEMBER OF f.collaborators')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        foreach ($dataFlows as $dataFlow) {
            $dataFlow->removeCollaborator($user);
            $this->entityManager->persist($dataFlow);
        }

        $this->userManager->deleteUser($user);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
