<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataTransformer;

use App\DataTransformer\CalculateDataTransformer;

class CalculateDataTransformerTest extends AbstractDataTransformerTest
{
    protected static $transformer = CalculateDataTransformer::class;

    public function dataProvider(): array
    {
        return [
            [
                [
                    'name' => 'sum of a and b',
                    'expression' => 'a + b',
                    'type' => 'int',
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
a,b,sum of a and b
1,2,3
3,4,7
5,6,11
7,8,15
CSV
                ),
            ],

            [
                [
                    'name' => 'a divided by b',
                    'expression' => 'a / b',
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
a,b,a divided by b
1,2,0.5
3,4,0.75
5,6,0.833333333
7,8,0.875
CSV
                ),
            ],
        ];
    }
}
