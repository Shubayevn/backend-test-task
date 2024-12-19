<?php

declare(strict_types=1);

namespace App\Controller;

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
        protected ValidatorInterface $validator)
    {
    }
    /**
     * @Route("/purchase", methods={"POST"})
     */
    #[Route('/purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validator->validate($data['taxNumber'], new TaxNumber());
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $result = $this->purchaseProcessor->process($data['product'], $data['taxNumber'], $data['couponCode'], $data['paymentProcessor']);

        if ($result['success']) {
            return new JsonResponse(['message' => 'Purchase successful'], 200);
        }

        return new JsonResponse(['errors' => $result['errors']], 400);
    }
}
