<?php


namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class DataSource implements \JsonSerializable
{
    /**
     * @var string
     */
    public $class;

    /**
     * @Required
     * @var string
     */
    public $name;

    /**
     * @Required
     * @var string
     */
    public $alias;

    /**
     * @Required
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $options;

    public function toArray(): array
    {
        return [
            'class' => $this->class,
            'name' => $this->name,
            'description' => $this->description,
            'options' => array_map(static function (Option $option) {
                return $option->toArray();
            }, $this->options ?? []),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
