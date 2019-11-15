<?php


namespace App\Message;


class RunDataFlowJobMessage
{
    private $dataFlowJobId;

    public function __construct(string $dataFlowJobId)
    {
        $this->dataFlowJobId = $dataFlowJobId;
    }

    public function getDataFlowJobId(): string
    {
        return $this->dataFlowJobId;
    }
}
