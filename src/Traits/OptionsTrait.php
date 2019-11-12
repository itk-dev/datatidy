<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Traits;

use App\Annotation\Exception\InvalidConfigurationException;
use App\Annotation\Exception\InvalidOptionException;
use App\Annotation\Exception\InvalidTypeException;
use App\Util\DataTypes;
use ReflectionProperty;

trait OptionsTrait
{
    /**
     * @var mixed
     */
    protected $metadata;

    /**
     * @var array
     */
    protected $options;

    public function getMetadata(): array
    {
        return $this->metadata ?? [];
    }

    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Set options on the transformer.
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        $this->validateAndApplyOptions($options);

        return $this;
    }

    protected function validateAndApplyOptions(array $options)
    {
        if (null === $this->metadata || !isset($this->metadata['options'])) {
            throw new InvalidOptionException('Missing metadata');
        }

        foreach ($this->metadata['options'] as $name => $option) {
            if ($option['required']) {
                $this->requireOption($name);
            }
            $value = $this->checkOptionType($name, $option['type'], $options);
            if (!property_exists($this, $name)) {
                throw new InvalidOptionException(sprintf('Property "%s" does not exist on %s.', $name, static::class));
            }
            $property = new ReflectionProperty($this, $name);
            $property->setAccessible(true);
            if (\array_key_exists($name, $options)) {
                $property->setValue($this, $value);
            } elseif (isset($option['default'])) {
                $property->setValue($this, $option['default']);
            }
        }
    }

    /**
     * @param array $value
     *
     * @see https://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     */
    protected function isAssoc($value): bool
    {
        if (!\is_array($value) || [] === $value) {
            return false;
        }

        return array_keys($value) !== range(0, \count($value) - 1);
    }

    protected function isMap($value): bool
    {
        if (!$this->isArray($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!$this->isAssoc($item) || !isset($item['from'], $item['to'])) {
                return false;
            }
        }

        return true;
    }

    protected function isArray($value): bool
    {
        return \is_array($value) && !$this->isAssoc($value);
    }

    protected function isString($value): bool
    {
        return \is_string($value);
    }

    protected function isInt($value): bool
    {
        return \is_int($value);
    }

    protected function isBool($value): bool
    {
        return \is_bool($value);
    }

    protected function isType($value): bool
    {
        return $this->isString($value) && \array_key_exists($value, DataTypes::$types);
    }

    protected function isReadable($objectOrArray, $propertyPath): bool
    {
        $propertyPath = $this->fixPropertyPath($propertyPath);

        return $this->getAccessor()->isReadable($objectOrArray, $propertyPath);
    }

    /**
     * Note: PropertyAccessor should/could be used, but apparently it does not really check existence of array values.
     *
     * @param $propertyPath
     *
     * @return array|mixed
     */
    protected function getOptionValue(array $value, $propertyPath)
    {
        $keys = explode('.', $propertyPath);
        foreach ($keys as $key) {
            if (!\array_key_exists($key, $value)) {
                throw (new InvalidKeyException($key))->setKey($key)->setValue($value);
            }
            $value = $value[$key];
        }

        return $value;
    }

    protected function requireOption(string $option): void
    {
        if (!\array_key_exists($option, $this->options)) {
            throw new InvalidConfigurationException(sprintf('missing option: %s', $option));
        }
    }

    protected function requireArray(string $key): void
    {
        $this->requireOption($key);

        if (!$this->isArray($this->options[$key])) {
            throw new InvalidConfigurationException(sprintf('must be an array: %s', $key));
        }
    }

    protected function requireMap(string $key): void
    {
        $this->requireOption($key);

        if (!$this->isMap($this->options[$key])) {
            throw new InvalidConfigurationException(sprintf('must be an map (associative array): %s', $key));
        }
    }

    protected function requireString(string $key): void
    {
        $this->requireOption($key);

        if (!$this->isString($this->options[$key])) {
            throw new InvalidConfigurationException(sprintf('must be a string: %s', $key));
        }
    }

    public function checkOptionType($name, $typeName, array $values)
    {
        if (\array_key_exists($name, $values)) {
            $value = $values[$name];
            switch ($typeName) {
                case 'bool':
                    if (!$this->isBool($value)) {
                        throw new InvalidTypeException(sprintf('Must be a bool: %s', $name));
                    }
                    break;
                case 'date':
                    $value = $this->createDateTime($value);
                    if (!$this->isDate($value)) {
                        throw new InvalidTypeException(sprintf('Must be a date: %s', $name));
                    }
                    break;
                case 'time':
                    $value = $this->createTime($value);
                    if (!$this->isDate($value)) {
                        throw new InvalidTypeException(sprintf('Must be a time: %s', $name));
                    }
                    break;
                case 'int':
                    if (!$this->isInt($value)) {
                        throw new InvalidTypeException(sprintf('Must be an int: %s', $name));
                    }
                    break;
                case 'string':
                case 'text':
                case 'column':
                    if (!$this->isString($value)) {
                        throw new InvalidTypeException(sprintf('Must be a string: %s', $name));
                    }
                    break;
                case 'type':
                    if (!$this->isType($value)) {
                        throw new InvalidTypeException(sprintf('Must be a type: %s', $name));
                    }
                    break;
                case 'columns':
                    if (!$this->isArray($value)) {
                        throw new InvalidTypeException(sprintf('Must be an array: %s', $name));
                    }
                    break;
                case 'map':
                    if (!$this->isMap($value)) {
                        throw new InvalidTypeException(sprintf('Must be a map: %s', $name));
                    }
                    break;
                case 'data_flow':
                case 'choice':
                    break;
                default:
                    throw new InvalidTypeException(sprintf('Unknown type: %s', $typeName));
            }

            return $value;
        }

        return null;
    }

    /**
     * Check that configuration value is a boolean if set. Otherwise, set a default value.
     */
    protected function checkBoolean(string $key, bool $default): void
    {
        if (\array_key_exists($key, $this->options)) {
            if (!$this->isBool($this->options[$key])) {
                throw new InvalidConfigurationException(sprintf('must be a boolean: %s', $key));
            }
        } else {
            $this->options[$key] = $default;
        }
    }
}
