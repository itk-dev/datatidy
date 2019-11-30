<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Annotation\Exception;

class AbstractOptionException extends \InvalidArgumentException
{
    private $path;

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path): self
    {
        $this->path = $path;

        return $this;
    }
}
