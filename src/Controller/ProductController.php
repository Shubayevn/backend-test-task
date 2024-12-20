<?php

namespace App\Controller;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Validator\CreateProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/product', name: 'app_product')]
    public function index(): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Product::class);
        $products = $repository->findAll();
        return $this->json($products);
    }

    #[Route('/product/create', name: 'app_product_create', methods: ['POST'])]
    #[CreateProduct]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $productDTO = new ProductDTO();
        $productDTO->name = $data['name'] ?? '';
        $productDTO->price = isset($data['price']) ? (float)$data['price'] : 0;

        $errors = $validator->validate($productDTO);

        if (count($errors) > 0) {
            return $this->json(['status' => 'error', 'errors' => (string) $errors]);
        }

        $product = new Product();
        $product->setName($productDTO->name);
        $product->setPrice($productDTO->price);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json(['status' => 'success', 'id' => $product->getId()]);
    }


    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product);
    }
}
