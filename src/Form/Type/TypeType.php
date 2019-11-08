<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type;

use App\Util\DataTypes;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TypeType extends ChoiceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->getChoices();
        $options['choice_loader'] = new CallbackChoiceLoader(static function () use ($choices) {
            return $choices;
        });

        parent::buildForm($builder, $options);
    }

    private function getChoices()
    {
        $names = DataTypes::getTypeNames();

        return ['' => '']
            + array_combine(
                array_map(static function ($name) {
                    return 'data_type.'.$name;
                }, $names),
                $names
            );
    }
}
