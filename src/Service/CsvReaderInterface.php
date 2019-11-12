<?php


namespace App\Service;


interface CsvReaderInterface
{
    public function read(string $csv): array;
}
