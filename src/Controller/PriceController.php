<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PriceCalculator;
use App\Validator\TaxNumber;
class PriceController extends AbstractController
{
    public function __construct(
        protected PriceCalculator $priceCalculator,
        protected ValidatorInterface $validator
    )
    {
    }
    /**
     * @Route("/calculate-price", methods={"POST"})
     */
    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validator->validate($data['taxNumber'], new TaxNumber());
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $price = $this->priceCalculator->calculate($data['product'], $data['taxNumber'], $data['couponCode']);

        return new JsonResponse(['price' => $price], 200);
    }
}
