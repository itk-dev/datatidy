<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataTransformer;

use App\Tests\ContainerTestCase;

abstract class AbstractDataTransformerTest extends ContainerTestCase
{
    /**
     * The transformer class.
     *
     * @var string
     */
    protected static $transformer = null;

    /**
     * @dataProvider dataProvider
     *
     * @param array                              $input
     * @param array|AbstractTransformerException $expected
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testTransformer(array $options, $input, $expected)
    {
        $transformer = $this->dataTransformerManager()
            ->getTransformer(static::$transformer)
            ->setOptions($options);

        if ($expected instanceof \Exception) {
            $this->expectExceptionObject($expected);
        }

        $actual = $transformer->transform($input);
        $this->assertEquals($expected->getColumns(), $actual->getColumns(), 'columns');
        $this->assertEquals(iterator_to_array($expected->rows()), iterator_to_array($actual->rows()), 'items');
    }

    abstract public function dataProvider();

    private $tableCounter = 0;

    protected function getTableName(string $suffix = null)
    {
        $name = preg_replace('@^([a-z]+\\\\)+@i', '', static::class);
        $name .= sprintf('%03d', $this->tableCounter);
        ++$this->tableCounter;

        return $name;
    }

    protected function buildFromCSV(string $csv, array $columns = null)
    {
        $data = $this->get('serializer')->decode($csv, 'csv', [
            'as_collection' => true,
        ]);

        return $this->buildFromData($data, $columns);
    }

    protected function buildFromData(array $data, array $columns = null)
    {
        $name = $this->getTableName();

        return $this->dataSetManager()->createDataSetFromData($name, $data, $columns);
    }
}
