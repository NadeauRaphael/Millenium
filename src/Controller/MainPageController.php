<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class MainPageController extends AbstractController
{
    private $em = null;

    #[Route('/', name: 'Catalog')]
    public function index(Request $request,ManagerRegistry $doctrine): Response
    {
        $this-> em = $doctrine -> getManager();

        $category = $request->query->get('category');
        $searchField = $request->request->get('search_field');

        $categories = $this->retrieveAllCategories();
        $products = $this->retrieveAllProduct($category, $searchField);

        return $this->render('Catalog/index.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
    private function retrieveAllProduct($category, $searchField) {
        return $this->em->getRepository(Product::class)->findWithCriteria($category, $searchField);
    }
    private function retrieveAllCategories(){
        return $this-> em -> getRepository(Categories::class) -> findAll();
    } 
}
