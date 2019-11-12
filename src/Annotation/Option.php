<?php


namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @see https://www.doctrine-project.org/projects/doctrine-annotations/en/1.7/custom.html
 *
 * @Annotation
 * @Target({"ANNOTATION", "PROPERTY"})
 */
class Option implements \JsonSerializable
{
    /**
     * @Required
     *
     * @Enum({"column", "columns", "string", "map", "bool", "int", "type", "choice"})
     *
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $choices;

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
        return [
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'required' => $this->required,
            'default' => $this->default,
        ];
    }
}
