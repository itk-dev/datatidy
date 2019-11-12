<?php


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
