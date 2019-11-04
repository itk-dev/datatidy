<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidTransform extends Constraint
{
    public $message = 'This is not a valid transform.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
