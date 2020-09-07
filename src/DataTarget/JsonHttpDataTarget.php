<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTarget;

use App\Annotation\DataTarget;
use App\Annotation\DataTarget\Option;
use Doctrine\Common\Collections\Collection;

/**
 * @DataTarget(
 *     name="JSON",
 *     description="Send data flow result to an HTTP endpoint.",
 * )
 */
class JsonHttpDataTarget extends AbstractHttpDataTarget
{
    /**
     * @Option(name="As object", description="Send data as a JSON object (the first row in the result)", type="choice", choices={"No": false, "Yes":true}, required=true)
     */
    private $asObject;

    protected function getPostOptions(array $rows, Collection $columns): array
    {
        return [
            'json' => $this->asObject ? reset($rows) : $rows,
        ];
    }
}
