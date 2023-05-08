<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\CategoryCollection;
use App\Form\CategoryCollectionFormType;
use App\Form\ProductFormType;
use Doctrine\ORM\Exception\ORMException;
use App\Form\UpdateProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


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
        $formCategories = $this->createForm(CategoryCollectionFormType::class, $categoriesCollection);

        $formCategories->handleRequest($request);

        if ($formCategories->isSubmitted() && $formCategories->isValid()) {
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
    #[Route('/admin/addProduct', name: 'admin_addProduct')]
    public function addProduct(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $productForm = $this->createForm(ProductFormType::class, $product);

        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $productImage = $productForm->get('image')->getData();
            if (!$productImage) {
                $this->HandleProductImage($productImage, $product, $slugger);
            }
        }
        return $this->render('admin/product.html.twig', [
            'formProduct' => $productForm->createView()
        ]);
    }
    #[Route('/admin/updateProduct/{idProduct}', name: 'admin_updateProduct')]
    public function updateProduct($idProduct, Request $request, SluggerInterface $slugger): Response
    {
        $product = $this->em->getRepository(Product::class)->find($idProduct);
        $productForm = $this->createForm(ProductFormType::class, $product);

        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $productImage = $productForm->get('image')->getData();
            if (!$productImage) {
                $this->HandleProductImage($productImage, $product, $slugger);
            }
        }
        return $this->render('admin/product.html.twig', [
            'formProduct' => $productForm->createView()
        ]);
    }
    #[Route('/admin/products', name: 'admin_products')]
    public function product(): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();
        return $this->render('admin/products.html.twig', [
            'products' => $products
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
    private function HandleProductImage($productPicture, $product, SluggerInterface $slugger)
    {
        if ($productPicture) {
            $originalFileName = pathinfo($productPicture->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = $slugger->slug($originalFileName);
            $newFileName = $safeFileName . "-" . uniqid() . $productPicture->guessExtension();
            try {
                $productPicture->move($this->getParameter('profile_picture_directory'), $newFileName);
                $product->setImgPath("/img/product/" . $newFileName);
                $this->em->persist($product);
                $this->em->flush();
            } catch (FileException $e) {
            } catch (ORMException) {
            }
        }
    }
}
