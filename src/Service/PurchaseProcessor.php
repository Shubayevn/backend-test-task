<?php
namespace App\Service;

use App\DTO\PurchaseDTO;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PurchaseProcessor
{

    public function __construct(
        protected PriceCalculator $priceCalculator,
        protected PaypalPaymentProcessor $paypalProcessor,
        protected StripePaymentProcessor $stripeProcessor,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    public function process(PurchaseDTO $purchaseDTO, Product $product, string $taxNumber, ?string $couponCode, string $paymentProcessor): array
    {
        $price = $this->priceCalculator->calculate($product, $taxNumber, $couponCode);

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

        if ($result['success']) {
            $order = new Order();
            $order->setProductId($product->getId());
            $order->setTaxNumber($purchaseDTO->tax_number);
            $order->setCouponCode($purchaseDTO->coupon_code);

            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }

        return $result;
    }
}