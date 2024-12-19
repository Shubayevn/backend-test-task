<?php
namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PurchaseProcessor
{

    public function __construct(
        protected PriceCalculator $priceCalculator,
        protected PaypalPaymentProcessor $paypalProcessor,
        protected StripePaymentProcessor $stripeProcessor
    )
    {
    }

    public function process(int $productId, string $taxNumber, ?string $couponCode, string $paymentProcessor): array
    {
        $price = $this->priceCalculator->calculate($productId, $taxNumber, $couponCode);

        switch ($paymentProcessor) {
            case 'paypal':
                $this->paypalProcessor->pay($price);
                $result = ['success' => true];
                break;
            case 'stripe':
                $result = ['success' => $this->stripeProcessor->processPayment($price)];
                break;
            default:
                return ['success' => false, 'errors' => 'Invalid payment processor'];
        }

        return $result;
    }
}