<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AbstractDataTransformRepository")
 * @Gedmo\Loggable()
 */
class AbstractDataTransform
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     * @Gedmo\SortablePosition()
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataFlow", inversedBy="transforms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dataFlow;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDataFlow(): ?DataFlow
    {
        return $this->dataFlow;
    }

    public function setDataFlow(?DataFlow $dataFlow): self
    {
        $this->dataFlow = $dataFlow;

        return $this;
    }
}
