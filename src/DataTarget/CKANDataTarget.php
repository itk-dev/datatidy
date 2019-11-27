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
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\TimeType;
use ItkDev\CKAN\API\Client\DataStore\DataStoreClient;

/**
 * Class DataTargetCKAN.
 *
 * @DataTarget(
 *     name="CKAN",
 *     description="Send result of data flow to a CKAN server."
 * )
 */
class CKANDataTarget extends AbstractDataTarget
{
    /**
     * @Option(name="Data source name", description="Name for data target", type="string")
     */
    private $name;

    /**
     * @Option(name="CKAN url", description="Url to a CKAN installation", type="string")
     */
    private $url;

    /**
     * @Option(name="CKAN API key", description="API key", type="string")
     */
    private $apiKey;

    /**
     * @Option(name="Append", type="bool", required=false, default=true, default=true, description="If not set, existing data will be deleted")
     */
    private $append;

    /**
     * @Option(name="Data set ID", type="string")
     */
    private $dataSetId;

    public function publish(array $rows, Collection $columns, array &$data)
    {
        $client = new DataStoreClient([
            'url' => $this->url,
            'api_key' => $this->apiKey,
        ]);
        $resourceId = $data['resourceId'] ?? null;
        if (null === $resourceId) {
            // Create new resource.
            $result = $client->dataStoreCreate($this->dataSetId, []);
            $resourceId = $result->toArray()['result']['resource_id'];

            // Set metadata.
            $result = $client->resourceUpdate($resourceId, [
                'name' => $this->name,
                'description' => sprintf('Created by DataTidy %s', (new DateTime())->format(DateTime::ATOM)),
            ]);
            $data['resourceId'] = $resourceId;
        }

        // Add fields and data.
        $fields = [];
        // @TODO: We can add new fields, but not change existing fields (or their type). How do we handle this?
        foreach ($columns as $name => $column) {
            $fields[] = [
                'id' => $name,
                'type' => $this->getFieldType($column),
            ];
        }
        // Store the fields for future reference, i.e. to check for changed types or deleted fields.
        $data['fields'] = $fields;

        $result = $client->dataStoreCreate(null, [
            'resource_id' => $resourceId,
            'fields' => $fields,
        ]);

        $records = [];
        foreach ($rows as $row) {
            $record = [];
            foreach ($row as $name => $value) {
                $record[$name] = $this->getFieldValue($value, $columns[$name]);
            }
            $records[] = $record;
        }

        $result = $client->dataStoreUpsert($resourceId, [
            // @TODO: A unique key is required for "upsert".
            'method' => $this->append ? 'insert' : 'upsert',
            'records' => $records,
        ]);

        $resourceUrl = sprintf('%s/dataset/%s/resource/%s', $this->url, $this->dataSetId, $resourceId);
        $this->info(sprintf('%d row(s) sent to CKAN (%s)', \count($rows), $resourceUrl));
    }

    /**
     * @see https://docs.ckan.org/en/ckan-2.7.3/maintaining/datastore.html#field-types
     */
    private function getFieldType(Column $column): ?string
    {
        $type = \get_class($column->getType());
        switch ($type) {
            case StringType::class:
            case TextType::class:
                return 'text';
            case JsonType::class:
                return 'json';
            case DateType::class:
                return 'date';
            case TimeType::class:
                return 'time';
            case DateTimeType::class:
                return 'timestamp';
            case IntegerType::class:
                return 'int';
            case FloatType::class:
                return 'float';
            case BooleanType::class:
                return 'bool';
            default:
                throw new \RuntimeException(sprintf('Unknown type: %s', $type));
        }
    }

    /**
     * @see https://docs.ckan.org/en/ckan-2.7.3/maintaining/datastore.html#field-types
     */
    private function getFieldValue($value, Column $column)
    {
        $type = $this->getFieldType($column);

        switch ($type) {
            case 'date':
                return $value->format('Y-m-d');
            case 'time':
                return $value->format('H:i:s');
            case 'timestamp':
                return $value->format(\DateTime::ATOM);
        }

        return $value;
    }
}
