<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CreateProductValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var CreateProduct $constraint */

        if (!is_array($value)) {
            $this->context->buildViolation('The value must be an array.')
                ->addViolation();
            return;
        }

        // Проверка поля "name"
        if (empty($value['name']) || !is_string($value['name'])) {
            $this->context->buildViolation($constraint->messageName)
                ->atPath('name') // Указывает на конкретное поле
                ->addViolation();
        }

        // Проверка поля "price"
        if (!isset($value['price']) || !is_float($value['price'])) {
            $this->context->buildViolation($constraint->messagePrice)
                ->atPath('price') // Указывает на конкретное поле
                ->addViolation();
        }
    }
}
