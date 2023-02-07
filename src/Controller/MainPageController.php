<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class MainPageController extends AbstractController
{
    private $em = null;

    #[Route('/', name: 'Catalog')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $this-> em = $doctrine -> getManager();

        $products = $this->retrieveAllProduct();


        return $this->render('Catalog/index.html.twig', [
            'products' => $products
        ]);
    }
    private function retrieveAllProduct() {

        return $this->em->getRepository(Product::class)->findAll();
        
    }
}
