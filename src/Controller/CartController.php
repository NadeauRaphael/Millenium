<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\Purchase;
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
        return $this-> redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{index}', name:'cart_delete')]
    public function deleteTodo($index, Request $request) : Response {
        $this->initSession($request);

        $this->purchases->delete($index);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/update', name:'cart_update', methods:['POST'])]
    public function updateTodo(Request $request) : Response {
        $post = $request->request->all();
        $this->initSession($request);
        $action = $request->request->get('action');
        if($action == "update") {
            $this->purchases->update($post);
        } 
        return $this->redirectToRoute('app_cart');
    }

    private function initSession(Request $request){
        $session = $request->getSession();
        $session -> set('name','Millenium');
        $this ->  purchases = $session->get('purchases', new Cart());
        $session->set('purchases', $this->purchases);
    }
}
