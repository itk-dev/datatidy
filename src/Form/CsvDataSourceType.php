<?php


namespace App\Form;


use App\Entity\CsvDataSource;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvDataSourceType extends AbstractDataSourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::getBaseFormBuilder($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CsvDataSource::class,
        ]);
    }
}
