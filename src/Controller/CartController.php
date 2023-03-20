<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $purchases;
    private $em = null;

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {

        $this -> initSession($request);
        $session = $request->getSession();
        return $this->render('cart/cart.html.twig', [
            'cart'=> $this->purchases
        ]);
    }
    #[Route('/cart/add/{idProduct}',name: 'cart_add')]
    public function addPurchase($idProduct,Request $request,ManagerRegistry $doctrine): Response
    {
        $this -> em = $doctrine -> getManager();
        $product = $this->em->getRepository(Product::class)->find($idProduct);

        $this -> initSession($request);
        $this -> purchases->add($product,1,$product->getPrice());
        return $this-> redirectToRoute('Catalog');
    }

    private function initSession(Request $request){
        $session = $request->getSession();
        $session -> set('name','Millenium');
        $this ->  purchases = $session->get('purchases', new Cart());
        $session->set('purchases', $this->purchases);
    }
}
