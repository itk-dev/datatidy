<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataTransformer;

use App\DataTransformer\Exception\InvalidKeyException;
use App\DataTransformer\FilterDataTransformer;

class FilterDataTransformerTest extends AbstractDataTransformerTest
{
    protected static $transformer = FilterDataTransformer::class;

    public function dataProvider(): array
    {
        return [
            [
                [
                    'column' => 'id',
                    'match' => '',
                    'replace' => '',
                ],
                $this->buildFromCSV(
                    <<<'CSV'
type,value
battery,12
temperature,87
CSV
                ),
                new InvalidKeyException('Invalid key: id'),
            ],

            [
                [
                    'column' => 'type',
                    'match' => 'battery',
                    'include' => true,
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
battery,12
CSV
                ),
            ],

            [
                [
                    'column' => 'value',
                    'match' => '123',
                    'include' => true,
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
temperature,123
CSV
                ),
            ],

            [
                [
                    'column' => 'type',
                    'match' => 'te',
                    'partial' => true,
                    'include' => true,
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
battery,12
temperature,123
CSV
                ),
            ],
        ];
    }
}
