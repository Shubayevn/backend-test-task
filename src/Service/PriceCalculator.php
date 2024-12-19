<?php

namespace App\Service;

class PriceCalculator
{
    /**
     * @throws \Exception
     */
    public function calculate(int $productId, string $taxNumber, ?string $couponCode): float
    {
        // Логика расчета цены с учетом налога и купона
        // Пример:
        $productPrice = $this->getProductPrice($productId);
        $taxRate = $this->getTaxRate($taxNumber);
        $discount = $this->getDiscount($couponCode);

        $priceWithTax = $productPrice + ($productPrice * $taxRate / 100);
        return $priceWithTax - ($priceWithTax * $discount / 100);
    }

    private function getProductPrice(int $productId): float
    {
        // Получение цены продукта из базы данных
        // Пример:
        switch ($productId) {
            case 1:
                return 100.0;
            case 2:
                return 20.0;
            case 3:
                return 10.0;
            default:
                throw new \Exception('Invalid product ID');
        }
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

        throw new \Exception('Invalid tax number');
    }

    private function getDiscount(?string $couponCode): float
    {
        // Получение скидки по купону
        switch ($couponCode) {
            case 'D15':
                return 15.0;
            case 'P10':
                return 10.0;
            case 'P100':
                return 100.0;
            default:
                return 0.0;
        }
    }
}