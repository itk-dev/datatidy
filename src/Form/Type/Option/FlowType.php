<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\Option;

use App\DataFlow\DataFlowManager;
use App\Entity\DataFlow;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Note: We extends ChoiceType (rather then EntityType) since we only want to get the DataFlow id.
 */
class FlowType extends ChoiceType
{
    /** @var DataFlowManager */
    private $dataFlowManager;

    public function __construct(DataFlowManager $dataFlowManager)
    {
        parent::__construct(null);
        $this->dataFlowManager = $dataFlowManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->getChoices();
        $options = array_replace($options, [
            'choices' => $choices,
            'placeholder' => '',
        ]);

        parent::buildForm($builder, $options);
    }

    private function getChoices()
    {
        $flows = $this->dataFlowManager->getDataFlows();

        return array_combine(
            array_map(function (DataFlow $dataFlow) {
                return $dataFlow->__toString();
            }, $flows),
            array_map(function (DataFlow $dataFlow) {
                return $dataFlow->getId();
            }, $flows)
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('class', DataFlow::class);
    }
}
