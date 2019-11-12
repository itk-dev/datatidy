<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Annotation;

use App\Annotation\DataTransformer\Option;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class DataTransformer extends AbstractAnnotation
{
    protected static $optionClass = Option::class;
}
