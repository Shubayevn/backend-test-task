<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class CreateProduct extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $messageName = 'The name field is required and must be a string.';
    public string $messagePrice = 'The price field is required and must be a float.';
}
