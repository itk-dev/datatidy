<?php


namespace App\Annotation;

use App\Annotation\DataTransformer\Option;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class DataSource extends AbstractAnnotation
{
    protected static $optionClass = Option::class;
}
