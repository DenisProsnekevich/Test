<?php

namespace App\Tests\Validator;

use App\Entity\Category;
use App\Validator\NoCircularReference;
use App\Validator\NoCircularReferenceValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NoCircularReferenceValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): NoCircularReferenceValidator
    {
        return new NoCircularReferenceValidator();
    }

    public function testValidHierarchy(): void
    {
        $root = new Category();
        $child = new Category();
        $child->setParent($root);

        $this->validator->validate($child, new NoCircularReference());

        $this->assertNoViolation();
    }

    public function testCircularReference(): void
    {
        $a = new Category();
        $b = new Category();
        $c = new Category();

        $a->setParent($c);
        $b->setParent($a);
        $c->setParent($b);

        $this->validator->validate($c, new NoCircularReference());

        $this->buildViolation('The selected category creates a cyclical hierarchy.')
            ->atPath('property.path.parent')
            ->assertRaised();
    }
}

