<?php

namespace App\DTO;

class PurchaseDTO
{
    public function __construct(
        public int $product_id,
        public string $tax_number,
        public ?string $coupon_code,
        public string $payment_processor
    ) {}


}