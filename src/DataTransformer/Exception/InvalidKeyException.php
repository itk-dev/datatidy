<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer\Exception;

class InvalidKeyException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $value;

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return InvalidKeyException
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @return InvalidKeyException
     */
    public function setValue(array $value): self
    {
        $this->value = $value;

        return $this;
    }
}
