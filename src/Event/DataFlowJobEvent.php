<?php

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
