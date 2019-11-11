<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

abstract class AbstractCommand extends Command
{
    /** @var TableStyle */
    protected $rightAligned;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->rightAligned = new TableStyle();
        $this->rightAligned->setPadType(STR_PAD_LEFT);
    }

    protected function setColumnStyle(Table $table, array $columns, TableStyle $style): void
    {
        foreach ($columns as $column) {
            $table->setColumnStyle($column, $style);
        }
    }

    protected function setAlignment(Table $table, array $columns, string $alignment): void
    {
        $style = null;

        switch ($alignment) {
            case 'right':
                $style = $this->rightAligned;
                break;
        }

        if (null === $style) {
            return;
        }

        $this->setColumnStyle($table, $columns, $style);
    }
}
