<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Annotation;

use App\Annotation\AbstractAnnotation\AbstractOption;

abstract class AbstractAnnotation implements \JsonSerializable
{
    protected static $optionClass = null;

    /**
     * The class this annotation is used on.
     *
     * @var string
     */
    public $class;

    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @Required
     *
     * @var string
     */
    public $description;

    /**
     * @var AbstractOption[]
     */
    public $options;

    public function toArray(): array
    {
        return [
            'class' => $this->class,
            'name' => $this->name,
            'description' => $this->description,
            'options' => array_map(static function (AbstractOption $option) {
                return $option->toArray();
            }, $this->options ?? []),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
