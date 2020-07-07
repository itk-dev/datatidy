<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository");
 * @ORM\Table(name="fos_user")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    protected $id;

    /**
     * @var DateTimeInterface
     * @ORM\Column(name="terms_accepted_at", type="datetime", nullable=true)
     */
    protected $termsAcceptedAt;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTermsAcceptedAt(): ?DateTimeInterface
    {
        return $this->termsAcceptedAt;
    }

    public function setTermsAcceptedAt(DateTimeInterface $termsAcceptedAt): self
    {
        $this->termsAcceptedAt = $termsAcceptedAt;

        return $this;
    }
}
