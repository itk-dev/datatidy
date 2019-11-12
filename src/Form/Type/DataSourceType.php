<?php

namespace App\Form\Type;

use App\DataSource\DataSourceManager;
use App\Entity\DataSource;
use App\Form\EventListener\AddOptionFieldsSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataSourceType extends AbstractType
{
    private $dataSourceManager;

    public function __construct(DataSourceManager $dataSourceManager)
    {
        $this->dataSourceManager = $dataSourceManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
        ;

        $dataSourceChoices = [];
        $dataSources = $this->dataSourceManager->getDataSources();
        foreach ($dataSources as $dataSource) {
            $dataSourceChoices[$dataSource['name']] = $dataSource['class'];
        }

        $builder
            ->add('dataSource', ChoiceType::class, [
                'choices' => $dataSourceChoices,
                'placeholder' => 'Choose an option',
            ]);

        $formModifier = function (FormInterface $form, $dataSource = null) {

            $options = [];
            if (is_null($dataSource)) {

                $this->dataSourceManager->getDataSourceOptions($dataSource);
            }

            $form->add('dataSourceOptions', TextType::class);
        };

        $builder->get('dataSource')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {

                $formModifier(
                    $event->getForm(),
                    $event->getData()->getDataSource()
                );
            }
        );

        $builder->get('dataSource')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {

                $formModifier(
                    $event->getForm()->getParent(),
                    $event->getForm()->getData()
                );
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
