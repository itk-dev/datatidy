<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer;

use App\Annotation\DataTransformer;
use App\Annotation\DataTransformer\Option;
use App\DataSet\DataSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Column;

/**
 * @DataTransformer(
 *     name="Filter",
 *     description="Filter on value"
 * )
 */
class FilterDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="column", help="Choose column")
     *
     * @var array
     */
    private $column;

    /**
     * @Option(type="string", description="Value to match")
     *
     * @var string
     */
    private $match;

    /**
     * @Option(type="bool", description="If set, a partial match. Otherwise the entire value must match. Ignored when using regular expressions.", required=false, default=false),
     *
     * @var bool
     */
    private $partial;

    /**
     * @Option(type="bool", description="Ignore case. Ignored when using regular expressions.", required=false, default=false),
     *
     * @var bool
     */
    private $ignoreCase;

    /**
     * @Option(type="bool", description="If set, use regular expressions for search and replace", required=false, default=false),
     *
     * @var bool
     */
    private $regexp;

    /**
     * @Option(type="bool", description="If not set, items that match will be removed rather that kept.", required=false, default=true),
     *
     * @var bool
     */
    private $include;

    public function transform(DataSet $input): DataSet
    {
        $columns = $this->transformColumns($input->getColumns());
        $output = $input->copy($columns->toArray())->createTable();

        foreach ($input->rows() as $row) {
            $value = $this->getValue($row, $this->column);
            $isMatch = false;
            if ($this->regexp) {
                $isMatch = preg_match($this->match, $value);
            } else {
                if ($this->partial) {
                    $isMatch = false !== ($this->ignoreCase ? stripos($value, $this->match) : strpos(
                        $value,
                        $this->match
                    ));
                } else {
                    $isMatch = 0 === ($this->ignoreCase ? strcasecmp($value, $this->match) : strcmp(
                        $value,
                        $this->match
                    ));
                }
            }

            if (($isMatch && $this->include)
                || (!$isMatch && !$this->include)) {
                $output->insertRow($row);
            }
        }

        return $output;
    }

    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        return $columns;
    }
}
