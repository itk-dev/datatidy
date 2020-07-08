<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\ReferenceManager;

interface ReferenceManagerInterface
{
    public function supports($entity): bool;

    public function getDeleteMessages($entity): array;

    public function delete($entity);
}
