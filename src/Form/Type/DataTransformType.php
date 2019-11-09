<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type;

use App\DataTransformer\DataTransformerManager;
use App\Entity\DataTransform;
use App\Util\OptionsFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataTransformType extends AbstractType
{
    /** @var DataTransformerManager */
    private $transformerManager;

    /** @var OptionsFormHelper */
    private $helper;

    public function __construct(DataTransformerManager $transformerManager, OptionsFormHelper $helper)
    {
        $this->transformerManager = $transformerManager;
        $this->helper = $helper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformers = $this->transformerManager->getTransformers();
        $transformerOptions = [];
        foreach ($transformers as $class => $transformer) {
            $transformerOptions[$transformer['name']] = $class;
        }

        $builder
            ->add('name', TextType::class)
            ->add('transformer', ChoiceType::class, [
                'choices' => $transformerOptions,
                'placeholder' => '',
            ]);

        // @see https://symfony.com/doc/current/form/dynamic_form_modification.html
        $formModifier = function (FormInterface $form, string $transformer) use ($options) {
            $this->helper->buildForm($form, $transformer, [
                'data_set_columns' => $options['data_set_columns'],
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) use ($formModifier) {
                /** @var DataTransform $transform */
                $transform = $event->getData();

                $formModifier($event->getForm(), $transform->getTransformer());
            }
        );

        $builder->get('transformer')->addEventListener(
            FormEvents::POST_SUBMIT,
            static function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $transformer = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $transformer);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DataTransform::class,
        ]);
        $resolver->setRequired('data_set_columns');
    }
}
