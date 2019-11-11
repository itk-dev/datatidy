<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTarget;

use App\Annotation\DataTarget;
use App\Annotation\DataTarget\Option;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Statement;

/**
 * @DataTarget(
 *     name="SQL",
 *     description="Send data flow result to a SQL server.")
 * )
 */
class DataTargetSQL extends AbstractDataTarget
{
    /**
     * @Option(name="Connection url", type="string")
     */
    private $url;

    /**
     * @Option(name="Table name", type="string")
     */
    private $tableName;

    /**
     * @Option(name="Append", type="bool", required=false, default=true, default=true, description="If not set, existing data will be deleted")
     */
    private $append;

    public function publish(array $rows, Collection $columns, array &$data)
    {
        /** @var Connection $connection */
        $connection = DriverManager::getConnection(['url' => $this->url]);
        $first = true;
        /** @var Statement $statement */
        $statement = null;
        $types = [];

        // @TODO: Should we (try to) create the database table if it does not exist?

        $tableName = $connection->getDatabasePlatform()->quoteIdentifier($this->tableName);
        if (!$this->append) {
            $connection->query(sprintf('TRUNCATE %s', $tableName))->execute();
        }
        foreach ($rows as $row) {
            if ($first) {
                $sql = sprintf(
                    'INSERT INTO %s(%s) VALUES (%s)',
                    $tableName,
                    implode(', ', array_map(static function ($name) use ($connection) {
                        return $connection->getDatabasePlatform()->quoteIdentifier($name);
                    }, array_keys($row))),
                    implode(', ', array_map(static function ($index) {
                        return ':p'.$index;
                    }, array_keys(array_keys($row))))
                );
                $statement = $connection->prepare($sql);

                $index = 0;
                foreach ($row as $name => $value) {
                    $types[$index] = $columns[$name]->getType();
                    ++$index;
                }
                $first = false;
            }

            foreach (array_values($row) as $index => $value) {
                $statement->bindValue(':p'.$index, $value, $types[$index]);
            }
            $statement->execute();
        }
        $this->info(sprintf('%d row(s) inserted into table %s', \count($rows), $this->tableName));
    }
}
