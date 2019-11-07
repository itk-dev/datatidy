<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer;

use App\Annotation\DataTransformer;
use App\Annotation\DataTransformer\Option;
use App\DataSet\DataSet;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @DataTransformer(
 *     name="Rename columns",
 *     description="Renames columns"
 * )
 */
class RenameColumnsDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="map")
     *
     * @var array
     */
    private $map;

    private $entityManager;

    public function transform(DataSet $input): DataSet
    {
        throw new \RuntimeException(__METHOD__.' not implemented');
    }

    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        // TODO: Implement transformColumns() method.
    }
}
