<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures\Processor;

use App\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserProcessor implements ProcessorInterface
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess(string $fixtureId, $object): void
    {
        if (!$object instanceof User) {
            return;
        }

        $object->setPassword($this->passwordEncoder->encodePassword($object, $object->getPassword()));
    }

    public function postProcess(string $id, $object): void
    {
        // TODO: Implement postProcess() method.
    }
}
