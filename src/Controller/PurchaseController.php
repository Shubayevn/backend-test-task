<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\PurchaseDTO;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PurchaseProcessor;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\TaxNumber;

class PurchaseController extends AbstractController
{
    public function __construct(
        protected PurchaseProcessor $purchaseProcessor,
        protected ValidatorInterface $validator,
    )
    {
    }
    /**
     * @Route("/purchase", methods={"POST"})
     */
    #[Route('{product}/purchase', methods: ['POST'])]
    public function purchase(Product $product, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validator->validate($data['taxNumber'], new TaxNumber());
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $invalidCoupons = ['D10', 'D15', 'D100'];
        if (!in_array($data['couponCode'], $invalidCoupons, true)) {
            return new JsonResponse(['errors' => 'Invalid coupon code'], 400);
        }

        $purchaseDTO = new PurchaseDTO(
            $product->getId(),
            $data['taxNumber'],
            $data['couponCode'],
            $data['paymentProcessor']
        );

        $result = $this->purchaseProcessor->process($purchaseDTO, $product, $data['taxNumber'], $data['couponCode'], $data['paymentProcessor']);

        if ($result['success']) {
            return new JsonResponse(['message' => 'Purchase successful'], 200);
        }

        return new JsonResponse(['errors' => $result['errors']], 400);
    }
}
