<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Validator\Constraints;

use App\DataTransformer\DataTransformerManager;
use App\Entity\DataTransform;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidTransformValidator extends ConstraintValidator
{
    /** @var TransformerManager */
    private $transformerManager;

    public function __construct(DataTransformerManager $transformerManager)
    {
        $this->transformerManager = $transformerManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidTransform) {
            throw new UnexpectedTypeException($constraint, ValidTransform::class);
        }
        if (!$value instanceof DataTransform) {
            throw new UnexpectedValueException(\get_class($value), DataTransform::class);
        }

        $transformer = $this->transformerManager->getTransformer($value->getTransformer());
        if (null === $transformer) {
            $this->context->buildViolation('Invalid transformer: {{ transformer }}')
                ->atPath('transformer')
                ->setParameter('{{ transformer }}', $value->getTransformer())
                ->addViolation();
        } else {
            try {
                $transformer = $this->transformerManager->getTransformer(
                    $value->getTransformer(),
                    $value->getTransformerOptions()
                );
            } catch (AbstractTransformerException $exception) {
                $this->context->buildViolation('Invalid transformer arguments: {{ message }}')
                    ->atPath('transformerArguments')
                    ->setParameter('{{ message }}', $exception->getMessage())
                    ->addViolation();
            }
        }
    }
}
