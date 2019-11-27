<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTarget;

use App\Annotation\DataTarget;
use Doctrine\Common\Collections\Collection;

/**
 * @DataTarget(
 *     name="JSON",
 *     description="Send data flow result to an HTTP endpoint.",
 * )
 */
class JsonHttpDataTarget extends AbstractHttpDataTarget
{
    protected function getPostOptions(array $rows, Collection $columns): array
    {
        return [
            'json' => $rows,
        ];
    }
}
