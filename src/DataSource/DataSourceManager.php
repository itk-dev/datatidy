<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSource;

use App\Entity\AbstractDataSource;

class DataSourceManager
{
    public function getData(AbstractDataSource $dataSource)
    {
        $data = [
            [
                'id' => 87,
                'name' => 'Mikkel',
                'birthday' => '1975-05-23',
            ],
            [
                'id' => 42,
                'name' => 'James',
                'birthday' => '1963-08-03',
            ],
        ];

        return $data;
    }
}
