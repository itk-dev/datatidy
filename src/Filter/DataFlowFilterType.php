<?php


namespace App\Filter;


use App\Entity\User;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\QueryBuilder;

class DataFlowFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $loggedInUser */
        $loggedInUser = $options['logged_in_user'];

        $builder->setMethod('GET');
        $builder->add('collaborators', Filters\ChoiceFilterType::class, [
            'label' => false,
            'placeholder' => false,
            'choices' => [
                'All flows' => 'all',
                'My flows' => $loggedInUser->getId(),
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

                if ('all' != $values['value']) {
                    $expression = $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('e.createdBy', ':'.$paramName),
                        $queryBuilder->expr()->isMemberOf(':'.$paramName, $field)
                    );
                }

                // expression parameters
                $parameters = array($paramName => $values['value']); // [ name => value ]

                return $filterQuery->createCondition($expression, $parameters);
            }
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
