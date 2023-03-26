<?php

namespace App\Controller;

use App\Entity\Category;
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
        $classCart = new CartController();
        $classCart  -> initSession($request);
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
    #[Route('/product/{idProduct}', name: 'product_modal')]
    public function infoProduct($idProduct, Request $request, ManagerRegistry $doctrine): Response
    {
        $this -> em = $doctrine -> getManager();
        $product = $this->em->getRepository(Product::class)->find($idProduct);

        return $this->render('Catalog/product.modal.twig', [
            'product' => $product , 
        ]);
    }
    private function retrieveAllProduct($category, $searchField) {
        return $this->em->getRepository(Product::class)->findWithCriteria($category, $searchField);
    }
    private function retrieveAllCategories(){
        return $this-> em -> getRepository(Category::class) -> findAll();
    } 
}
