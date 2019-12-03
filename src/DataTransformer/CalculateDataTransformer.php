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
use App\DataTransformer\Exception\InvalidColumnException;
use App\Util\DataTypes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\Keywords\MariaDb102Keywords;
use Doctrine\DBAL\Schema\Column;

/**
 * @DataTransformer(
 *     name="Calculate",
 *     description="Calculate value",
 * )
 */
class CalculateDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="string", description="The column to put the expression result into")
     *
     * @var string
     */
    private $name;

    /**
     * @Option(type="string", description="The expression to calculate")
     *
     * @var string
     */
    private $expression;

    /**
     * @Option(type="type", description="The type of the expression result")
     *
     * @var string
     */
    private $type;

    public function transform(DataSet $input): DataSet
    {
        $columns = $input->getColumns();
        $newColumns = $this->transformColumns($input->getColumns());

        [$names, $quotedExpression] = $this->getQuoteNamesInExpression($this->expression, $input);
        // Only existing columns can be used in calculation.
        $invalidNames = array_diff($names, $columns->getKeys());
        if (!empty($invalidNames)) {
            throw new InvalidColumnException(sprintf('Invalid names: %s', implode(', ', $invalidNames)));
        }

        $expressions = $input->getQuotedColumnNames();
        $expressions[$this->name] = $quotedExpression;

        $output = $input->copy($newColumns->toArray())->createTable();

        $sql = sprintf(
            'INSERT INTO %s(%s) SELECT %s FROM %s;',
            $output->getQuotedTableName(),
            implode(', ', $output->getQuotedColumnNames()),
            implode(', ', $expressions),
            $input->getQuotedTableName()
        );

        return $output->buildFromSQL($sql);
    }

    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        $type = DataTypes::getType($this->type);

        $columns[$this->name] = new Column($this->name, $type);

        return $columns;
    }

    /**
     * Quote names in expression and return the quoted expression along with a list of unquoted names.
     */
    private function getQuoteNamesInExpression(string $expression, DataSet $dataSet): array
    {
        // Replace string literals with (hopefully) unique tokens.
        $strings = [];
        $expression = preg_replace_callback('/"([\\\\"]|[^"])*"/', function ($match) use (&$strings) {
            $token = sprintf('({[%03d]})', \count($strings));
            $strings[$token] = $match['0'];

            return $token;
        }, $expression);

        // Collect names and unquote the ones escaped with backticks.
        $names = [];
        if (false !== preg_match_all('/(?P<name>(?:[a-z_][a-z0-9_]*|`[^`]+`))/i', $expression, $matches)) {
            foreach ($matches['name'] as $name) {
                $unquoted = trim($name, '`');
                if (!$this->isKeyword($name)) {
                    $names[$name] = $unquoted;
                }
            }
        }

        // Order names by length.
        uksort($names, static function ($a, $b) {
            return \strlen($b) - \strlen($a);
        });

        foreach ($names as $key => $name) {
            $expression = str_replace($key, $dataSet->getQuotedColumnName($name), $expression);
        }

        // Put strings back into the expression.
        $expression = str_replace(array_keys($strings), array_values($strings), $expression);

        return [$names, $expression];
    }

    private $keywordList;

    // Additional sql keywords.
    private $keywords = [
        'CONCAT',
    ];

    private function isKeyword(string $word): bool
    {
        if (null === $this->keywordList) {
            $this->keywordList = new MariaDb102Keywords();
        }

        if ($this->keywordList->isKeyword($word)) {
            return true;
        }

        return \in_array(strtoupper($word), $this->keywords, true);
    }
}
