<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    public float $price;
}