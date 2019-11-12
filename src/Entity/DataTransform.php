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
 * @ORM\Entity(repositoryClass="App\Repository\DataTransformRepository")
 * @Gedmo\Loggable
 * @ AppAssert\ValidTransform
 */
class DataTransform
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="No transformer specified")
     * @Gedmo\Versioned
     */
    private $transformer;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank(message="No transformer options specified")
     * @Assert\Valid
     * @Gedmo\Versioned
     */
    private $transformerOptions = [];

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\SortablePosition
     * @Gedmo\Versioned
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataFlow", inversedBy="transforms")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\SortableGroup
     * @Gedmo\Versioned
     */
    private $dataFlow;

    public function getId(): ?string
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

    public function getTransformer(): ?string
    {
        return $this->transformer;
    }

    public function setTransformer(string $transformer): self
    {
        $this->transformer = $transformer;

        return $this;
    }

    public function getTransformerOptions(): ?array
    {
        return $this->transformerOptions;
    }

    public function setTransformerOptions(array $transformerOptions): self
    {
        $this->transformerOptions = $transformerOptions;

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

    public function setDataFlow(DataFlow $dataFlow): self
    {
        $this->dataFlow = $dataFlow;

        return $this;
    }
}
