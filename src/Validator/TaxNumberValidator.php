<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNumberValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_string($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', gettype($value))
                ->addViolation();
            return;
        }

        if (!$this->isValidTaxNumber($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isValidTaxNumber(string $taxNumber): bool
    {
        return preg_match('/^DE\d{9}$/', $taxNumber) ||
            preg_match('/^IT\d{11}$/', $taxNumber) ||
            preg_match('/^GR\d{9}$/', $taxNumber) ||
            preg_match('/^FR[A-Z]{2}\d{9}$/', $taxNumber);
    }
}