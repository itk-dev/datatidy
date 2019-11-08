<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Util;

use App\Form\Type\ColumnStringMapType;
use App\Form\Type\ColumnsType;
use App\Form\Type\MapType;
use App\Form\Type\TypeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormInterface;

class OptionsFormHelper
{
    public function buildForm(FormInterface $form, array $options)
    {
        foreach ($options as $name => $option) {
            $type = $this->getFormType($option);
            $formOptions = $this->getFormOptions($option);
            $form->add($name, $type, $formOptions);
        }
    }

    public function getFormType(array $option): string
    {
        if (isset($option['formType'])) {
            return $option['formType'];
        }

        $type = $option['type'] ?? null;

        switch ($type) {
            case 'bool':
                return CheckboxType::class;
            case 'date':
                return DateType::class;
            case 'choice':
                return ChoiceType::class;
            case 'time':
                return TimeType::class;
            case 'int':
                return IntegerType::class;
            case 'text':
                return TextareaType::class;
            case 'columns':
                return ColumnsType::class;
            case 'map':
                return MapType::class;
            case 'type':
                return TypeType::class;
            default:
                throw new \RuntimeException(sprintf('Invalid type: %s', $type));
        }

        return TextType::class;
    }

    public function getFormOptions(array $option, $value = null): array
    {
        $options = [
            'label' => $option['name'],
            'required' => $option['required'],
            'help' => $option['help'],
        ];

        $type = $this->getFormType($option);
        switch ($type) {
            case ChoiceType::class:
                $options['choices'] = $option->choices;
                break;
            case ColumnsType::class:
            case ColumnStringMapType::class:
                $options['columns'] = ['a', 'b', 'c', __METHOD__];
                break;
        }

        if (2 === \func_num_args()) {
            $options['data'] = $this->getFormData($value, $option);
        }

        return $options;
    }

    public function getFormData($value, Option $option)
    {
        $type = $this->getFormType($option);

        switch ($type) {
            case DateType::class:
            case DateTimeType::class:
            case TimeType::class:
                return $this->createDateTime($value);
        }

        return $value;
    }

    private function createDateTime($value)
    {
        if ($value instanceof DateTime) {
            return $value;
        } elseif (isset($value['hour'], $value['minute'])) {
            return (new DateTime('@0'))->setTime((int) $value['hour'], (int) $value['minute']);
        } elseif (isset($value['date'], $value['timezone'])) {
            return new DateTime($value['date'], new DateTimeZone($value['timezone']));
        } elseif (\is_string($value)) {
            try {
                return new DateTime($value);
            } catch (\Exception $exception) {
            }
        }

        return null;
    }
}
