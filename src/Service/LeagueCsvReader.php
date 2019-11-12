<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use League\Csv\Reader;

class LeagueCsvReader implements CsvReaderInterface
{
    public function read(string $csv): array
    {
        $reader = Reader::createFromString($csv);

        return iterator_to_array($reader->getRecords());
    }
}
