<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
        protected ValidatorInterface $validator,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @Route("/calculate-price/{product}", methods={"POST"})
     * @throws Exception
     */
    #[Route('/calculate-price/{product}', methods: ['POST'])]
    public function calculatePrice(Product $product,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $errors = $this->validator->validate($data['taxNumber'], new TaxNumber());
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }


        try {
            $price = $this->priceCalculator->calculate($product, $data['taxNumber'], $data['couponCode']);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse(['price' => $price], 200);
    }
}
