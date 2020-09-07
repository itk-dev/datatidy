<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataFlowJobLogEntryRepository")
 */
class DataFlowJobLogEntry
{
    use TimestampableEntity;

    const LEVEL_INFO = 'info';
    const LEVEL_ERROR = 'error';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataFlowJob", inversedBy="logEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $level;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getJob(): ?DataFlowJob
    {
        return $this->job;
    }

    public function setJob(?DataFlowJob $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        if (!\in_array($level, [self::LEVEL_INFO, self::LEVEL_ERROR])) {
            throw new \InvalidArgumentException('Invalid level: '.$level);
        }

        $this->level = $level;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
