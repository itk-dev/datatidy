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
use Doctrine\Common\Collections\Collection;

class DataFlowRunResult
{
    /** @var DataFlow */
    private $dataFlow;

    /** @var array */
    private $options;

    /** @var ArrayCollection */
    private $transformResults;

    /** @var ArrayCollection */
    private $transformExceptions;

    /** @var DataSet */
    private $lookahead;

    /** @var \Exception */
    private $lookaheadException;

    /** @var bool */
    private $published = false;

    /** @var ArrayCollection */
    private $publishResults;

    /** @var \Exception */
    private $publishExceptions;

    /** @var int */
    private $numberOfSteps;

    /** @var int */
    private $totalNumberOfSteps;

    public function __construct(DataFlow $dataFlow, array $options)
    {
        $this->dataFlow = $dataFlow;
        $this->options = $options;
        $this->transformResults = new ArrayCollection();
        $this->transformExceptions = new ArrayCollection();
        $this->publishResults = new ArrayCollection();
        $this->publishExceptions = new ArrayCollection();

        $this->numberOfSteps = 0;
        $this->totalNumberOfSteps = $this->dataFlow->getTransforms()->count() + 1;
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
        $this->transformResults[] = $dataSet;

        ++$this->numberOfSteps;

        return $this;
    }

    public function getTransformResults(): ArrayCollection
    {
        return $this->transformResults;
    }

    public function getLastTransformResult(): ?DataSet
    {
        return $this->isSuccess() ? $this->transformResults->last() : null;
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

    public function addTransformException(\Exception $exception): self
    {
        $this->transformExceptions[] = $exception;

        ++$this->numberOfSteps;

        return $this;
    }

    /**
     * @return Collection|\Exception[]
     */
    public function getTransformExceptions(): Collection
    {
        return $this->transformExceptions;
    }

    public function getTransformException(): ?\Exception
    {
        return $this->hasTransformException() ? $this->getTransformExceptions()->first() : null;
    }

    public function hasTransformException(): bool
    {
        return !$this->getTransformExceptions()->isEmpty();
    }

    public function isSuccess(): bool
    {
        return !$this->getTransformResults()->isEmpty() && !$this->hasTransformException();
    }

    /**
     * A run is complete iff the number of data sets equals the number of transforms + 1.
     */
    public function isComplete(): bool
    {
        return $this->dataFlow->getTransforms()->count() + 1 === $this->transformResults->count();
    }

    public function isPublished(): bool
    {
        return $this->published && $this->isComplete() && 0 === $this->getPublishExceptions()->count();
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function addPublishResult($result): self
    {
        $this->publishResults[] = $result;

        return $this;
    }

    public function addPublishException(\Exception $exception): self
    {
        $this->publishExceptions[] = $exception;

        return $this;
    }

    /**
     * @return Collection|\Exception[]
     */
    public function getPublishExceptions(): Collection
    {
        return $this->publishExceptions;
    }

    public function getPublishException(): ?\Exception
    {
        return $this->hasPublishException() ? $this->getPublishExceptions()->first() : null;
    }

    public function hasPublishException(): bool
    {
        return !$this->getPublishExceptions()->isEmpty();
    }

    public function getNumberOfSteps(): int
    {
        return $this->numberOfSteps;
    }

    public function getTotalNumberOfSteps(): int
    {
        return $this->totalNumberOfSteps;
    }
}
