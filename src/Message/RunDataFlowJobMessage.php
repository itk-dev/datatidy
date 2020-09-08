<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

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
