<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSource;

use App\Traits\OptionsTrait;

abstract class AbstractDataSource implements DataSourceInterface
{
    use OptionsTrait;

    abstract public function pull();
}
