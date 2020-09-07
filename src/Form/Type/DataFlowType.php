<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type;

use App\Entity\DataFlow;
use App\Entity\DataSource;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class DataFlowType extends AbstractType
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var DataFlow|null $flow */
        $flow = $builder->getData();

        $builder
            ->add(
                $builder->create('settings', FormType::class, [
                    'inherit_data' => true,
                ])
                    ->add('name', TextType::class)
                    ->add('collaborators', Select2EntityType::class, [
                        'class' => User::class,
                        'multiple' => true,
                        'required' => false,
                        'remote_route' => 'data_flow_collaborator_search',
                        'remote_params' => [
                            'id' => $flow->getId(),
                            '_format' => 'select2',
                        ],
                        'minimum_input_length' => 6,
                        'placeholder' => 'Enter email',
                        'help' => 'Enter email addresses of users that you want to give access to edit this data flow',
                    ])
                    ->add('schedule', ChoiceType::class, [
                        'choices' => [
                            'Every minute' => '*/1 * * * *',
                            'Every 15 minutes' => '*/15 * * * *',
                            'Every 30 minutes' => '*/30 * * * *',
                            'Every hour' => '*/60 * * * *',
                            'Everyday at midnight' => '0 0 * * *',
                            'Every week' => '0 0 * * 0',
                            'Every month' => '0 0 1 * *',
                        ],
                        'choice_translation_domain' => true,
                        'help' => $options['schedule_help'],
                    ])
                    ->add('enabled', CheckboxType::class, [
                        'help' => 'Check if you want to have this flow run automatically',
                        'required' => false,
                    ])
            )
            ->add(
                $builder->create('data_source', FormType::class, [
                        'inherit_data' => true,
                ])
                    ->add('dataSource', EntityType::class, [
                        'class' => DataSource::class,
                        'placeholder' => '',
                    ])
            )
            ->add(
                $builder->create('data_targets', FormType::class, [
                        'inherit_data' => true,
                ])
                    ->add('dataTargets', CollectionType::class, [
                        'entry_type' => DataTargetType::class,
                        'entry_options' => [
                            'label' => false,
                            'block_prefix' => 'data_flow_target_item',
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'block_prefix' => 'data_flow_target',
                    ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DataFlow::class,
            'schedule_help' => 'Not scheduled yet.',
        ]);
    }
}
