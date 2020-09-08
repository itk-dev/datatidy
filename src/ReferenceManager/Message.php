<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\ReferenceManager;

class Message
{
    public const DANGER = 'danger';
    public const WARNING = 'warning';

    /** @var string */
    private $message;

    /** @var string */
    private $type;

    /** @var array|null */
    private $data;

    public function __construct(string $message, string $type = self::DANGER, array $data = null)
    {
        $this->message = $message;
        $this->type = $type;
        $this->data = $data;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return Message
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Message
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @return Message
     */
    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
