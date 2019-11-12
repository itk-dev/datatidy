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

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataTargetRepository")
 */
class DataTarget
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dataTarget;

    /**
     * @ORM\Column(type="json")
     */
    private $dataTargetOptions = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataFlow", inversedBy="dataTargets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dataFlow;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDataTarget(): ?string
    {
        return $this->dataTarget;
    }

    public function setDataTarget(string $dataTarget): self
    {
        $this->dataTarget = $dataTarget;

        return $this;
    }

    public function getDataTargetOptions(): ?array
    {
        return $this->dataTargetOptions;
    }

    public function setDataTargetOptions(array $dataTargetOptions): self
    {
        $this->dataTargetOptions = $dataTargetOptions;

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

    public function __toString()
    {
        return $this->dataTarget ?? static::class;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
