<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Traits\ControllerFlashDataTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;

class AbstractController extends BaseAbstractController
{
    use ControllerFlashDataTrait;
}
