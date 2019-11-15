<?php


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
