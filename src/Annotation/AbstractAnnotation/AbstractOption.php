<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Annotation\AbstractAnnotation;

abstract class AbstractOption implements \JsonSerializable
{
    /**
     * @var int
     */
    public $order = 0;

    /**
     * @Required
     *
     * @Enum({"column", "columns", "collapse_columns", "string", "map", "bool", "int", "type", "choice", "data_flow"})
     *
     * @var string
     */
    public $type;

    /**
     * @var bool
     */
    public $nullable = false;

    /**
     * @var array
     */
    public $choices;

    /**
     * @var string
     */
    public $formType;

    /**
     * Name to use in stead of property name.
     *
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $help;

    /**
     * @var bool
     */
    public $required = true;

    /**
     * Default value.
     *
     * @var mixed
     */
    public $default;

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
