<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'products')]
    public function listProducts(): Response
    {
        return $this->render('product/index.html.twig', [
            'listProducts' => 'Liste des produits',
        ]);
    }

    #[Route('/product/{id}', name: 'product')]
    public function viewProduct(int $id): Response
    {
        return $this->render('product/index.html.twig', [
            'viewProduct' => $id,
        ]);
    }
}
