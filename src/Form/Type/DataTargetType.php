<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type;

use App\DataTarget\DataTargetManager;
use App\Entity\DataTarget;
use App\Util\OptionsFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataTargetType extends AbstractType
{
    /** @var DataTargetManager */
    private $manager;

    /** @var OptionsFormHelper */
    private $helper;

    public function __construct(DataTargetManager $manager, OptionsFormHelper $helper)
    {
        $this->manager = $manager;
        $this->helper = $helper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $targets = $this->manager->getDataTargets();
        $targetOptions = [];
        foreach ($targets as $class => $target) {
            $targetOptions[$target['name']] = $class;
        }

        $builder
            ->add('description', TextType::class)
            ->add('dataTarget', ChoiceType::class, [
                'choices' => $targetOptions,
                'placeholder' => '',
                'attr' => [
                    'class' => 'data-target-options',
                    'data-options-form' => 'dataTargetOptions',
                ],
            ]);

        // @see https://symfony.com/doc/current/form/dynamic_form_modification.html
        $formModifier = function (FormInterface $form, string $dataTarget = null) {
            $this->helper->buildForm($form, $this->manager->getDataTargetOptions($dataTarget), 'dataTargetOptions');
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) use ($formModifier) {
                /** @var DataTarget $dataTarget */
                $dataTarget = $event->getData();

                $formModifier($event->getForm(), $dataTarget ? $dataTarget->getDataTarget() : null);
            }
        );

        $builder->get('dataTarget')->addEventListener(
            FormEvents::POST_SUBMIT,
            static function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $dataTarget = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $dataTarget);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DataTarget::class,
        ]);
    }
}
