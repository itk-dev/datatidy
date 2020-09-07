<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Event;

use App\Entity\DataFlowJob;

class DataFlowJobTimeOutEvent extends DataFlowJobEvent
{
    private $timeoutThreshold;

    public function __construct(DataFlowJob $job, int $timeoutThreshold)
    {
        parent::__construct($job);
        $this->timeoutThreshold = $timeoutThreshold;
    }

    public function getTimeoutThreshold(): int
    {
        return $this->timeoutThreshold;
    }
}
