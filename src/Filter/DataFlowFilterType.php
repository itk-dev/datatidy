<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Filter;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class DataFlowFilterType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $builder->setMethod('GET');
        $builder->add('collaborators', Filters\ChoiceFilterType::class, [
            'label' => false,
            'placeholder' => false,
            'choices' => [
                'All flows' => 'all',
                'My flows' => $currentUser->getId(),
            ],
            'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                if (empty($values['value'])) {
                    return null;
                }

                $paramName = sprintf('p_%s', str_replace('.', '_', $field));

                // expression that represent the condition
                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = $filterQuery->getQueryBuilder();

                $expression = null;

                if ('all' !== $values['value']) {
                    $expression = $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('e.createdBy', ':'.$paramName),
                        $queryBuilder->expr()->isMemberOf(':'.$paramName, $field)
                    );
                }

                // expression parameters
                $parameters = [$paramName => $values['value']]; // [ name => value ]

                return $filterQuery->createCondition($expression, $parameters);
            },
        ]);
    }

    public function getBlockPrefix()
    {
        return 'data_flow_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'logged_in_user' => null,
        ]);
    }
}
