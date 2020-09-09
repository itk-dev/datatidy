<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class UserType extends AbstractType
{
    /** @var RoleHierarchyInterface */
    private $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Assume that all roles are reachable from ROLE_ADMIN.
        $roleNames = $this->roleHierarchy->getReachableRoleNames(['ROLE_ADMIN']);
        // Human names, e.g. ROLE_USER_ADMIN -> User admin
        $roleHumanNames = array_map(static function ($role) {
            return ucfirst(strtolower(str_replace('_', ' ', preg_replace('/^ROLE_/', '', $role))));
        }, $roleNames);

        $builder
            ->add('email')
            ->add('enabled')
            ->add('roles', ChoiceType::class, [
                'choices' => array_combine($roleHumanNames, $roleNames),
                'expanded' => true,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
