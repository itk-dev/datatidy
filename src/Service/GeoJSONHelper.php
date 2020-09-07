<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use proj4php\Point;
use proj4php\Proj;
use proj4php\Proj4php;

class GeoJSONHelper
{
    /**
     * Change Coordinate Reference System in a GeoJSON object.
     *
     * @return array
     */
    public function changeCRS(array $geojson, string $sourceCRS, string $targetCRS)
    {
        $proj4 = new Proj4php();
        $source = new Proj($sourceCRS, $proj4);
        $target = new Proj($targetCRS, $proj4);

        array_walk($geojson, function (&$item) use ($proj4, $source, $target) {
            if ($this->isCoordinate($item)) {
                $sourcePoint = new Point(...array_merge($item, [$source]));
                $targetPoint = $proj4->transform($target, $sourcePoint);
                $item = \array_slice($targetPoint->toArray(), 0, \count($item));
            }
        });

        return $geojson;
    }

    /**
     * Determine if a value is a GeoJSON point (an array of floats).
     *
     * @param $item
     */
    private function isCoordinate($item): bool
    {
        if (!\is_array($item)) {
            return false;
        }

        // A point must have 2 or 3 values.
        if (!\in_array(\count($item), [2, 3], true)) {
            return false;
        }

        foreach ($item as $value) {
            if (!(\is_float($value) || \is_int($value))) {
                return false;
            }
        }

        return true;
    }
}
