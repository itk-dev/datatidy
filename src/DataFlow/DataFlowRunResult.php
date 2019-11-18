<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFlow;

use App\DataSet\DataSet;
use App\Entity\DataFlow;
use Doctrine\Common\Collections\ArrayCollection;

class DataFlowRunResult
{
    /** @var DataFlow */
    private $dataFlow;

    /** @var array */
    private $options;

    /** @var ArrayCollection */
    private $dataSets;

    /** @var ArrayCollection */
    private $exceptions;

    /** @var ArrayCollection */
    private $results;

    /** @var DataSet */
    private $lookahead;

    /** @var \Exception */
    private $lookaheadException;

    public function __construct(DataFlow $dataFlow, array $options)
    {
        $this->dataFlow = $dataFlow;
        $this->options = $options;
        $this->dataSets = new ArrayCollection();
        $this->exceptions = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    public function getDataFlow(): DataFlow
    {
        return $this->dataFlow;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function addDataSet(DataSet $dataSet): self
    {
        $this->dataSets[] = $dataSet;
        $this->results[] = $dataSet;

        return $this;
    }

    public function getDataSets(): ArrayCollection
    {
        return $this->dataSets;
    }

    public function getLastDataSet(): ?DataSet
    {
        return $this->isSuccess() ? $this->dataSets->last() : null;
    }

    public function getLookahead(): ?DataSet
    {
        return $this->lookahead;
    }

    public function setLookahead(DataSet $lookahead): self
    {
        $this->lookahead = $lookahead;

        return $this;
    }

    public function getLookaheadException(): ?\Exception
    {
        return $this->lookaheadException;
    }

    public function setLookaheadException(\Exception $lookaheadException): self
    {
        $this->lookaheadException = $lookaheadException;

        return $this;
    }

    public function addException(\Exception $exception): self
    {
        $this->exceptions[] = $exception;
        $this->results[] = $exception;

        return $this;
    }

    public function getExceptions(): ArrayCollection
    {
        return $this->exceptions;
    }

    public function getException(): ?\Exception
    {
        return $this->exceptions->first() ?: null;
    }

    public function hasException(): bool
    {
        return !$this->exceptions->isEmpty();
    }

    public function isSuccess(): bool
    {
        return 0 === $this->getExceptions()->count();
    }

    /**
     * A run is complete iff the number of data sets equals the number of transforms + 1.
     */
    public function isComplete(): bool
    {
        return $this->dataFlow->getTransforms()->count() + 1 === $this->dataSets->count();
    }

    public function getResults(): ArrayCollection
    {
        return $this->results;
    }
}
