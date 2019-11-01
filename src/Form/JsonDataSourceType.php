<?php


namespace App\Form;


use App\Entity\JsonDataSource;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JsonDataSourceType extends AbstractDataSourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder = parent::getBaseFormBuilder($builder, $options);

        $builder->add('root');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JsonDataSource::class,
        ]);
    }
}
