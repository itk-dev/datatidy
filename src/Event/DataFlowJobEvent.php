<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Event;

use App\Entity\DataFlowJob;
use Symfony\Contracts\EventDispatcher\Event;

abstract class DataFlowJobEvent extends Event
{
    private $job;

    public function __construct(DataFlowJob $job)
    {
        $this->job = $job;
    }

    public function getJob(): DataFlowJob
    {
        return $this->job;
    }
}
