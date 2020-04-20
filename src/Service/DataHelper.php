<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use RuntimeException;

class DataHelper
{
    public function expand(array $data, string $key): array
    {
        if (empty($data)) {
            throw new RuntimeException('Cannot expand empty data');
        }
        if ($this->isAssoc($data)) {
            $data = [$data];
        }
        if ($this->isAssoc($data)) {
            throw new RuntimeException('Cannot expand associative array');
        }
        $row = reset($data);
        $types = $this->getTypes($row);
        if (!isset($types[$key])) {
            throw new RuntimeException(sprintf('Invalid key: %s', $key));
        }
        switch ($types[$key]) {
            case self::TYPE_ARRAY:
                return $this->expandArray($data, $key);
            case self::TYPE_OBJECT:
                return $this->expandObject($data, $key);
            default:
                throw new RuntimeException(sprintf('Cannot expand data of type %s', $types[$key]));
        }
    }

    public function collapse(array $data, string $key): array
    {
        if (empty($data)) {
            throw new RuntimeException('Cannot collapse empty data');
        }
        if ($this->isAssoc($data)) {
            throw new RuntimeException('Cannot collapse associative array');
        }

        $row = reset($data);

        return \array_key_exists($key, $row)
            ? $this->collapseArray($data, $key)
            : $this->collapseObject($data, $key);
    }

    private function getRowId(array $row, string $excludeKey): string
    {
        return json_encode(array_filter($row, static function ($name) use ($excludeKey) {
            return $name !== $excludeKey;
        }, ARRAY_FILTER_USE_KEY), JSON_THROW_ON_ERROR, 512);
    }

    public function collapseArray(array $data, string $key, bool $includeNullValues = false): array
    {
        $collapsed = [];
        foreach ($data as &$row) {
            $rowId = $this->getRowId($row, $key);
            if (!isset($collapsed[$rowId])) {
                $collapsed[$rowId] = $row;
                $collapsed[$rowId][$key] = [];
            }
            $collapsed[$rowId][$key][] = $row[$key];
        }

        return array_values($collapsed);
    }

    public function collapseObject(array $data, string $key, bool $includeNullValues = false): array
    {
        foreach ($data as &$row) {
            foreach ($row as $name => $value) {
                if (preg_match('/^'.preg_quote($key, '/').'\.(?<name>.+)/', $name, $matches)) {
                    if ($includeNullValues || null !== $value) {
                        $row[$key][$matches['name']] = $value;
                    }
                    unset($row[$name]);
                }
            }
        }

        return $data;
    }

    public function expandArray(array $data, string $key): array
    {
        $expanded = [];

        foreach ($data as $row) {
            $value = $row[$key];
            if (!$this->isArray($value)) {
                throw new RuntimeException(sprintf('Expand value must be an array; got %s', $this->getType($value)));
            }
            foreach ($value as $k => $v) {
                $row[$key] = $v;
                $expanded[] = $row;
            }
        }

        return $expanded;
    }

    public function expandObject(array $data, string $key): array
    {
        // Keep track of expanded to make sure that all rows have the same keys
        $expandedKeys = [];

        foreach ($data as &$row) {
            $value = $row[$key];
            if (!$this->isObject($value)) {
                throw new RuntimeException(sprintf('Expand value must be an object; got %s', $this->getType($value)));
            }
            unset($row[$key]);
            foreach ($value as $k => $v) {
                $row[$key.'.'.$k] = $v;
                $expandedKeys[$key.'.'.$k] = null;
            }
        }

        // Make sure that all rows have the same keys
        foreach ($data as &$row) {
            $row += $expandedKeys;
        }

        return $data;
    }

    public function isAssoc(array $arr): bool
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, \count($arr) - 1);
    }

    public function isScalar($value): bool
    {
        return !($this->isArray($value) || $this->isObject($value));
    }

    private function isArray($value): bool
    {
        return \is_array($value) && !$this->isAssoc($value);
    }

    private function isObject($value): bool
    {
        return \is_array($value) && $this->isAssoc($value);
    }

    public const TYPE_ARRAY = 'array';
    public const TYPE_OBJECT = 'object';
    public const TYPE_SCALAR = 'scalar';

    public function getType($value): string
    {
        if ($this->isArray($value)) {
            return self::TYPE_ARRAY;
        }
        if ($this->isObject($value)) {
            return self::TYPE_OBJECT;
        }

        return self::TYPE_SCALAR;
    }

    public function getTypes(array $data): array
    {
        return array_map([$this, 'getType'], $data);
    }
}