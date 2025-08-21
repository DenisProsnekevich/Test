<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NoCircularReference extends Constraint
{
    public string $message = 'The selected category creates a cyclical hierarchy.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
