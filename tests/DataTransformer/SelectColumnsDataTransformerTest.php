<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataTransformer;

use App\DataTransformer\Exception\InvalidColumnException;
use App\DataTransformer\SelectColumnsDataTransformer;

class SelectColumnsDataTransformerTest extends AbstractDataTransformerTest
{
    protected static $transformer = SelectColumnsDataTransformer::class;

    public function dataProvider(): array
    {
        return [
            [
                [
                    'columns' => ['first name'],
                ],
                $this->buildFromCSV(
                    <<<'CSV'
name
Mikkel
CSV
                ),
                new InvalidColumnException('invalid columns: first name'),
            ],

            [
                [
                    'columns' => ['name'],
                ],
                $this->buildFromCSV(
                    <<<'CSV'
name,birthday
Mikkel,1975-05-23
CSV
                ),
                $this->buildFromCSV(
                    <<<'CSV'
name
Mikkel
CSV
                ),
            ],

            [
                [
                    'columns' => ['name'],
                    'include' => false,
                ],
                $this->buildFromCSV(
                    <<<'CSV'
name,birthday
Mikkel,1975-05-23
CSV
                ),
                $this->buildFromCSV(
                    <<<'CSV'
birthday
1975-05-23
CSV
                ),
            ],
        ];
    }
}
