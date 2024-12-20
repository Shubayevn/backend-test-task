<?php

namespace App\Service;

use App\Entity\Product;
use Exception;

class PriceCalculator
{
    /**
     * @throws Exception
     */
    public function calculate(Product $product, string $taxNumber, ?string $couponCode): float
    {
        // Логика расчета цены с учетом налога и купона
        $productPrice = $this->getProductPrice($product);
        $taxRate = $this->getTaxRate($taxNumber);
        $discount = $this->getDiscount($couponCode);

        $priceWithTax = $productPrice + ($productPrice * $taxRate / 100);
        return $priceWithTax - ($priceWithTax * $discount / 100);
    }

    /**
     * @throws Exception
     */
    private function getProductPrice(Product $product): float
    {
        // Получение цены продукта из базы данных
        $price = $product->getPrice();
        if ($price === 0.0 || $price === null) {
            throw new Exception('Price is not set');
        }
        return $price;
    }

    private function getTaxRate(string $taxNumber): float
    {
        // Определение налоговой ставки по налоговому номеру
        if (preg_match('/^DE\d{9}$/', $taxNumber)) {
            return 19.0;
        }

        if (preg_match('/^IT\d{11}$/', $taxNumber)) {
            return 22.0;
        }

        if (preg_match('/^GR\d{9}$/', $taxNumber)) {
            return 24.0;
        }

        if (preg_match('/^FR[A-Z]{2}\d{9}$/', $taxNumber)) {
            return 20.0;
        }

        throw new Exception('Invalid tax number');
    }

    private function getDiscount(?string $couponCode): float
    {
        // Получение скидки по купону
        switch ($couponCode) {
            case 'P10':
                return 10.0;
            case 'D15':
                return 15.0;
            case 'P100':
                return 100.0;
            default:
                return 0.0;
        }
    }
}