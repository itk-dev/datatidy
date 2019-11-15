<?php


namespace App\Message;


class RunDataFlowMessage
{
    private $dataFlowId;

    public function __construct(string $dataFlowId)
    {
        $this->dataFlowId = $dataFlowId;
    }

    public function getDataFlowId(): string
    {
        return $this->dataFlowId;
    }
}
