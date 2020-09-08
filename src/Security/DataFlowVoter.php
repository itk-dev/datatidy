<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Security;

use App\Entity\DataFlow;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class DataFlowVoter extends Voter
{
    // these strings are just invented: you can use anything
    public const VIEW = 'view';
    public const EDIT = 'edit';

    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::EDIT])) {
            return false;
        }

        if (!$subject instanceof DataFlow) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        /** @var DataFlow $dataFlow */
        $dataFlow = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($dataFlow, $user);
            case self::EDIT:
                return $this->canEdit($dataFlow, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(DataFlow $dataFlow, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($dataFlow, $user)) {
            return true;
        }

        // For now all users can view all data flows.
        return true;
    }

    private function canEdit(DataFlow $dataFlow, User $user)
    {
        // The owner (creator) and collaborators can edit the data flow.
        return $user === $dataFlow->getCreatedBy()
            || $dataFlow->getCollaborators()->contains($user);
    }
}
