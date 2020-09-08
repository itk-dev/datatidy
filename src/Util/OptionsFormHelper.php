<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Util;

use App\DataTransformer\DataTransformerManager;
use App\Entity\DataTransform;
use App\Form\Type\Option\CollapseColumnsType;
use App\Form\Type\Option\ColumnStringMapType;
use App\Form\Type\Option\ColumnsType;
use App\Form\Type\Option\ColumnType;
use App\Form\Type\Option\FlowType;
use App\Form\Type\Option\MapType;
use App\Form\Type\Option\TypeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormInterface;

class OptionsFormHelper
{
    /** @var DataTransformerManager */
    private $transformerManager;

    /** @var array */
    private $options;

    public function __construct(DataTransformerManager $transformerManager)
    {
        $this->transformerManager = $transformerManager;
    }

    public function buildForm(FormInterface $form, array $serviceOptions, string $serviceOptionsName, array $options = [], DataTransform $transform = null)
    {
        $form->add($serviceOptionsName, FormType::class);

        if (!empty($serviceOptions)) {
            $transformerOptions = null !== $transform ? $transform->getTransformerOptions() : [];
            $optionsForm = $form->get($serviceOptionsName);
            $this->options = $options;
            foreach ($serviceOptions as $name => $option) {
                $type = $this->getFormType($option);
                $formOptions = $this->getFormOptions($option);
                if (\array_key_exists($name, $transformerOptions)) {
                    $formOptions['data'] = $transformerOptions[$name];
                }
                $optionsForm->add($name, $type, $formOptions);
            }
            $this->options = null;
        }
    }

    private function getOption(string $key)
    {
        return $this->options[$key];
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
            case 'string':
                return TextType::class;
            case 'text':
                return TextareaType::class;
            case 'column':
                return ColumnType::class;
            case 'columns':
                return ColumnsType::class;
            case 'map':
                return MapType::class;
            case 'type':
                return TypeType::class;
            case 'data_flow':
                return FlowType::class;
            case 'collapse_columns':
                return CollapseColumnsType::class;
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
            'help' => $option['help'] ?? $option['description'],
        ];

        if (null !== $option['default']) {
            $options['data'] = $option['default'];
        }

        $type = $this->getFormType($option);
        switch ($type) {
            case ChoiceType::class:
                $choices = $option['choices'] ?? [];
                if (Helper::isArray($choices)) {
                    $choices = array_combine($choices, $choices);
                }
                $options['choices'] = $choices;
                break;
            case ColumnType::class:
            case ColumnsType::class:
            case ColumnStringMapType::class:
            case CollapseColumnsType::class:
                $options['data_set_columns'] = $this->getOption('data_set_columns');
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
