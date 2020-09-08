<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Event;

use App\DataFlow\DataFlowRunResult;
use App\Entity\DataFlowJob;

class DataFlowJobFailedEvent extends DataFlowJobEvent
{
    private $result;

    public function __construct(DataFlowJob $job, DataFlowRunResult $result)
    {
        parent::__construct($job);
        $this->result = $result;
    }

    public function getResult(): DataFlowRunResult
    {
        return $this->result;
    }
}
