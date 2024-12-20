<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @Route("/purchase", methods={"GET"})
     */
    #[Route('/order', name: 'app_order')]
    public function index(): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Order::class);
        $purchase = $repository->findAll();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'list' => $purchase,
        ]);
    }
}
