<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTarget;

use App\Traits\LogTrait;
use App\Traits\OptionsTrait;
use Doctrine\Common\Collections\Collection;

abstract class AbstractDataTarget
{
    use LogTrait;
    use OptionsTrait;

    abstract public function publish(array $rows, Collection $columns, array &$data);
}
