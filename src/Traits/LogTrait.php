<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Traits;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

/**
 * A simple trait that implements the log method.
 */
trait LogTrait
{
    use LoggerAwareTrait;
    use LoggerTrait;

    public function log($level, $message, array $context = [])
    {
        if (null !== $this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
