<?php

namespace App\Validator;

use App\Entity\Category;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoCircularReferenceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoCircularReference) {
            throw new UnexpectedTypeException($constraint, NoCircularReference::class);
        }

        if (!$value instanceof Category) {
            return;
        }

        $parent = $value->getParent();
        $current = $parent;

        while ($current !== null) {
            if ($current === $value) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('parent')
                    ->addViolation();

                return;
            }
            $current = $current->getParent();
        }
    }
}
