<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataTransformer;

use App\DataTransformer\Exception\InvalidColumnException;
use App\DataTransformer\ReplaceValuesDataTransformer;

class ReplaceValuesDataTransformerTest extends AbstractDataTransformerTest
{
    protected static $transformer = ReplaceValuesDataTransformer::class;

    public function dataProvider(): array
    {
        return [
            [
                [
                    'columns' => ['id'],
                    'replacements' => [
                        [
                            'from' => '',
                            'to' => '',
                        ],
                    ],
                ],
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,12
temperature,87
CSV
                ),
                new InvalidColumnException('invalid columns: id'),
            ],

            [
                [
                    'columns' => ['value'],
                    'replacements' => [
                        [
                            'from' => '12',
                            'to' => 'low',
                        ],
                    ],
                ],
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,12
temperature,123
CSV
                ),
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,low
temperature,123
CSV
                ),
            ],

            [
                [
                    'columns' => ['value'],
                    'replacements' => [
                        [
                            'from' => '12',
                            'to' => 'low',
                        ],
                    ],
                    'partial' => true,
                ],
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,12
temperature,123
CSV
                ),
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,low
temperature,low3
CSV
                ),
            ],

            [
                [
                    'columns' => ['value'],
                    'replacements' => [
                        [
                            'from' => '/[0-9]+/',
                            'to' => '\0\0x',
                        ],
                    ],
                    'regexp' => true,
                ],
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,12
temperature,123
CSV
                ),
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,1212x
temperature,123123x
CSV
                ),
            ],
        ];
    }
}
