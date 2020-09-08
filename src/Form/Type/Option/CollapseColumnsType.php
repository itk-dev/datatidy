<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\Option;

use App\DataSet\DataSetColumnList;
use App\DataTransformer\ExpandColumnDataTransformer;

class CollapseColumnsType extends ColumnsType
{
    protected function getChoices(array $options)
    {
        /** @var DataSetColumnList $columns */
        $columns = $options['data_set_columns'];
        $names = $columns->getNames();

        // Get names containing at least on expand column delimiter.
        $paths = array_filter(array_map(static function ($name) {
            return \array_slice(explode(ExpandColumnDataTransformer::DELIMITER, $name), 0, -1);
        }, $names));
        // Get max path length.
        $maxPathLength = array_reduce($paths, static function ($carry, $item) {
            return max(\count($item), $carry);
        }, 0);
        // Keep only longest paths.
        $paths = array_filter($paths, static function ($path) use ($maxPathLength) {
            return \count($path) === $maxPathLength;
        });
        // Implode paths to names.
        $pathNames = array_map(static function ($path) {
            return implode(ExpandColumnDataTransformer::DELIMITER, $path);
        }, $paths);

        if (\count($pathNames) > 0) {
            $names = $pathNames;
        }

        return array_combine($names, $names);
    }
}
