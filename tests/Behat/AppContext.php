<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Behat;

use Behatch\Context\BaseContext;

class AppContext extends BaseContext
{
    /**
     * Presses the nth specified button.
     *
     * @When (I )fill in the :index :field with :value
     */
    public function fillInTheNthField($index, $field, $value)
    {
        $field = $this->findField('named', ['field', $field], $index);
        $field->setValue($value);
    }

    protected function findField($selector, $locator, $index)
    {
        $page = $this->getSession()->getPage();
        $nodes = $page->findAll($selector, $locator);

        if (!isset($nodes[$index - 1])) {
            throw new \RuntimeException(sprintf('The %s %s %s was not found anywhere in the page', json_encode($index), json_encode($selector), json_encode($locator)));
        }

        return $nodes[$index - 1];
    }
}
