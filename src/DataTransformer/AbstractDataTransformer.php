<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer;

use App\DataSet\DataSet;
use App\Traits\OptionsTrait;
use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractDataTransformer
{
    use OptionsTrait;

    /**
     * Transform a data set to a new data set.
     */
    abstract public function transform(DataSet $input): DataSet;

    /**
     * Transform columns.
     */
    abstract public function transformColumns(ArrayCollection $columns): ArrayCollection;
}
