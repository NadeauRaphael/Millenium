<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Form\CategoryCollection;
use App\Form\CategoryCollectionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $em = null;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/admin/category', name: 'admin_category')]
    public function addCategory(Request $request): Response
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        $categoriesCollection = new CategoryCollection($categories);
        $formCategories = $this->createForm(CategoryCollectionFormType::class,$categoriesCollection);

        $formCategories->handleRequest($request);

        if($formCategories->isSubmitted() && $formCategories->isValid()){
            $newCollectionItems = $formCategories->getData()->getCategories();
            foreach ($newCollectionItems as $newItem) {
                $this->em->persist($newItem);
            }
            $this->em->flush();
        }

        return $this->render('admin/categories.html.twig', [
            'formCategories' => $formCategories
        ]);
    }
    #[Route('/admin/updateCategory', name: 'admin_updateCategory')]
    public function updatecategory(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/product', name: 'admin_product')]
    public function addProduct(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/orders', name: 'admin_orders')]
    public function orders(): Response
    {
        $orders = $this->em->getRepository(Order::class)->findAll();
        return $this->render('Order/orders.html.twig', [
            'orders'      => $orders
        ]);
    }
}
