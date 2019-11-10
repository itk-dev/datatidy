<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataFlowRepository")
 * @Gedmo\Loggable()
 */
class DataFlow
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
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AbstractDataSource", inversedBy="dataFlows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dataSource;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DataTransform", mappedBy="dataFlow", cascade={"persist"}, orphanRemoval=true)
     */
    private $transforms;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned()
     */
    private $enabled;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned()
     */
    private $ttl;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastRunAt;

    public function __construct()
    {
        $this->transforms = new ArrayCollection();
        $this->enabled = false;
        $this->ttl = 60 * 60;
    }

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

    /**
     * @return mixed
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return DataFlow
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDataSource(): ?AbstractDataSource
    {
        return $this->dataSource;
    }

    public function setDataSource(?AbstractDataSource $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * @return Collection|DataTransform[]
     */
    public function getTransforms(): Collection
    {
        return $this->transforms;
    }

    public function addTransform(DataTransform $transform): self
    {
        if (!$this->transforms->contains($transform)) {
            $this->transforms[] = $transform;
            $transform->setDataFlow($this);
        }

        return $this;
    }

    public function removeTransform(DataTransform $transform): self
    {
        if ($this->transforms->contains($transform)) {
            $this->transforms->removeElement($transform);
            // set the owning side to null (unless already changed)
            if ($transform->getDataFlow() === $this) {
                $transform->setDataFlow(null);
            }
        }

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function getLastRunAt(): ?\DateTimeInterface
    {
        return $this->lastRunAt;
    }

    public function setLastRunAt(?\DateTimeInterface $lastRunAt): self
    {
        $this->lastRunAt = $lastRunAt;

        return $this;
    }

    public function __toString()
    {
        return sprintf('%s (#%s)', $this->name ?? static::class, $this->id);
    }
}
