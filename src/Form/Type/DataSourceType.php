<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type;

use App\DataSource\DataSourceManager;
use App\Entity\DataSource;
use App\Util\OptionsFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataSourceType extends AbstractType
{
    private $dataSourceManager;

    /** @var OptionsFormHelper */
    private $helper;

    public function __construct(DataSourceManager $dataSourceManager, OptionsFormHelper $helper)
    {
        $this->dataSourceManager = $dataSourceManager;
        $this->helper = $helper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dataSources = $this->dataSourceManager->getDataSources();
        $dataSourceChoices = [];
        foreach ($dataSources as $dataSource) {
            $dataSourceChoices[$dataSource['name']] = $dataSource['class'];
        }

        $builder
            ->add(
                $builder->create('settings', FormType::class, [
                    'inherit_data' => true,
                    'label' => null,
                ])
                    ->add('name')
                    ->add('description')
                    ->add('ttl', NumberType::class)
            )
            ->add('dataSource', ChoiceType::class, [
                'choices' => $dataSourceChoices,
                'placeholder' => '',
            ])
        ;

        // @see https://symfony.com/doc/current/form/dynamic_form_modification.html
        $formModifier = function (FormInterface $form, string $dataSource = null) {
            $this->helper->buildForm(
                $form,
                $this->dataSourceManager->getDataSourceOptions($dataSource),
                'dataSourceOptions'
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) use ($formModifier) {
                $form = $event->getForm();
                $dataSource = $event->getData();

                $formModifier($form, $dataSource->getDataSource());
            }
        );

        $builder->get('dataSource')->addEventListener(
            FormEvents::POST_SUBMIT,
            static function (FormEvent $event) use ($formModifier) {
                $form = $event->getForm()->getParent();
                $dataSource = $event->getForm()->getData();

                $formModifier($form, $dataSource);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DataSource::class,
        ]);
    }
}
