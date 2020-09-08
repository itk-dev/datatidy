<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataFlowJobRepository")
 */
class DataFlowJob
{
    use TimestampableEntity;

    const STATUS_CREATED = 'created';
    const STATUS_RUNNING = 'running';
    const STATUS_QUEUED = 'queued';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataFlow", inversedBy="jobs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dataFlow;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DataFlowJobLogEntry", mappedBy="job", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $logEntries;

    public function __construct()
    {
        $this->logEntries = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $statuses = [
            self::STATUS_CREATED,
            self::STATUS_RUNNING,
            self::STATUS_QUEUED,
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ];

        if (!\in_array($status, $statuses)) {
            throw new \InvalidArgumentException('Invalid status: '.$status);
        }

        $this->status = $status;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return Collection|DataFlowJobLogEntry[]
     */
    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }

    public function addLogEntry(DataFlowJobLogEntry $logEntry): self
    {
        if (!$this->logEntries->contains($logEntry)) {
            $this->logEntries[] = $logEntry;
            $logEntry->setJob($this);
        }

        return $this;
    }

    public function removeLogEntry(DataFlowJobLogEntry $logEntry): self
    {
        if ($this->logEntries->contains($logEntry)) {
            $this->logEntries->removeElement($logEntry);
            // set the owning side to null (unless already changed)
            if ($logEntry->getJob() === $this) {
                $logEntry->setJob(null);
            }
        }

        return $this;
    }
}
