<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataTransformer;

use App\DataTransformer\ChangeColumnTypeDataTransformer;

class ChangeColumnTypeDataTransformerTest extends AbstractDataTransformerTest
{
    protected static $transformer = ChangeColumnTypeDataTransformer::class;

    public function dataProvider(): array
    {
        return [
            [
                [
                    'columns' => ['a', 'b'],
                    'type' => 'float',
                ],
                $this->buildFromCSV(
                    <<<'CSV'
a,b
1,2
3,4
5,6
7,8
CSV
                ),
                $this->buildFromCSV(
                    <<<'CSV'
a,b
1.0,2.0
3.0,4.0
5.0,6.0
7.0,8.0
CSV
                ),
            ],
        ];
    }
}
