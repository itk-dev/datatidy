<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Security;

use App\Entity\DataSource;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class DataSourceVoter extends Voter
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

        if (!$subject instanceof DataSource) {
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

        /** @var DataSource $dataSource */
        $dataSource = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($dataSource, $user);
            case self::EDIT:
                return $this->canEdit($dataSource, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(DataSource $dataSource, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($dataSource, $user)) {
            return true;
        }

        // For now all users can view all data flows.
        return true;
    }

    private function canEdit(DataSource $dataSource, User $user)
    {
        // The owner (creator) edit the data flow.
        return $user === $dataSource->getCreatedBy();
    }
}
