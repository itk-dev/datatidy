<?php

namespace App\Form;

use FOS\UserBundle\Form\Type\ResettingFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class ResettingPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array(
                'translation_domain' => 'FOSUserBundle',
                'attr' => array(
                    'autocomplete' => 'new-password',
                    'class' => 'form-control'
                ),
            ),
            'first_options' => [
                'label' => 'form.new_password',
            ],
            'second_options' => [
                'label' => 'form.new_password_confirmation',
            ],
            'invalid_message' => 'fos_user.password.mismatch',
        ));
    }

    public function getParent()
    {
        return ResettingFormType::class;
    }
}
