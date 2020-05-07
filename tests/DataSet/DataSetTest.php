<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSet;

use App\Tests\ContainerTestCase;
use Doctrine\DBAL\Types\Type;

class DataSetTest extends ContainerTestCase
{
    /**
     * @dataProvider guessTypesProvider
     */
    public function testGuessTypes(array $rows, array $expected)
    {
        $dataSet = new DataSet(__METHOD__, $this->getContainer()->get('database_connection'));

        $actual = $dataSet->guessTypes($rows);
        $this->assertEquals($expected, $actual);
    }

    public function guessTypesProvider(): array
    {
        return [
            [
                [],
                [],
            ],

            [
                [
                    ['value' => 1.0],
                    ['value' => 2.1],
                ],
                [
                    'value' => Type::FLOAT,
                ],
            ],

            [
                [
                    ['value' => 0],
                    ['value' => 87],
                ],
                [
                    'value' => Type::INTEGER,
                ],
            ],

            [
                [
                    ['value' => '00'],
                    ['value' => '087'],
                ],
                [
                    'value' => Type::FLOAT,
                ],
            ],

            [
                [
                    ['value' => '00'],
                    ['value' => '087'],
                    ['value' => 'k'],
                ],
                [
                    'value' => Type::STRING,
                ],
            ],

            [
                [
                    ['value' => '0.1'],
                    ['value' => '0.87'],
                ],
                [
                    'value' => Type::FLOAT,
                ],
            ],
        ];
    }
}
