<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Anonymizer;

use App\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;

class UserAnonymizer
{
    /** @var UserManagerInterface */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function anonymize(User $user)
    {
        if (!$this->isAnonymized($user)) {
            $email = sha1(random_bytes(10)).'@localhost';
            $user->setEmail($email);
            $this->userManager->updateUser($user);
        }
    }

    public function isAnonymized(User $user)
    {
        $email = $user->getEmail();

        return preg_match('/@localhost$/', $user->getEmail());
    }
}
