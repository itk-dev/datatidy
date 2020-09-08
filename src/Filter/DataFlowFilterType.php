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
        $builder
            ->add('query', Filters\TextFilterType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Search …',
                ],
                'apply_filter' => static function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }
                    $paramName = sprintf('p_%s', str_replace('.', '_', $field));
                    $alias = $values['alias'];
                    // expression that represent the condition
                    /** @var QueryBuilder $queryBuilder */
                    $queryBuilder = $filterQuery->getQueryBuilder();

                    $expression = $queryBuilder->expr()->orX();
                    $parameters = [];
                    $searchFieldNames = [
                        'name',
                    ];
                    if (!empty($searchFieldNames)) {
                        foreach ($searchFieldNames as $fieldName) {
                            $expression->add(
                                $queryBuilder->expr()->like($alias.'.'.$fieldName, ':'.$paramName)
                            );
                        }
                        $parameters[$paramName] = '%'.$values['value'].'%';
                    }

                    return $filterQuery->createCondition($expression, $parameters);
                },
            ])
            ->add('collaborators', Filters\ChoiceFilterType::class, [
                'label' => false,
                'placeholder' => false,
                'choices' => [
                    'All flows' => 'all',
                    'My flows' => $currentUser->getId(),
                ],
                'apply_filter' => static function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }

                    $paramName = sprintf('p_%s', str_replace('.', '_', $field));

                    // expression that represent the condition
                    /** @var QueryBuilder $queryBuilder */
                    $queryBuilder = $filterQuery->getQueryBuilder();

                    $expression = null;
                    $parameters = [];

                    if ('all' !== $values['value']) {
                        $expression = $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('e.createdBy', ':'.$paramName),
                            $queryBuilder->expr()->isMemberOf(':'.$paramName, $field)
                        );
                        $parameters[$paramName] = $values['value'];
                    }

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
            'csrf_protection' => false,
        ]);
    }
}
