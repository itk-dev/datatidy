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
use App\DataTransformer\RenameColumnsDataTransformer;

class RenameColumnsTransformerTest extends AbstractDataTransformerTest
{
    protected static $transformer = RenameColumnsDataTransformer::class;

    public function dataProvider(): array
    {
        return [
            [
                [
                    'renames' => [
                        [
                            'from' => 'birthdate',
                            'to' => 'birthday',
                        ],
                    ],
                ],
                $this->buildFromCSV(
                    <<<'CSV'
birthdate
1975-05-23
CSV
                ),
                $this->buildFromCSV(
                    <<<'CSV'
birthday
1975-05-23
CSV
                ),
            ],

            [
                [
                    'renames' => [
                        [
                            'from' => 'a',
                            'to' => 'b',
                        ],
                    ],
                ],
                $this->buildFromCSV(
                    <<<'CSV'
aa,b
1,2
CSV
                ),
                new InvalidColumnException('Column "a" does not exist'),
            ],

            [
                [
                    'renames' => [
                        [
                            'from' => 'a',
                            'to' => 'b',
                        ],
                    ],
                ],
                $this->buildFromCSV(
                    <<<'CSV'
a,b
1,2
CSV
                ),
                new InvalidColumnException('Column "b" already exists'),
            ],
        ];
    }
}
