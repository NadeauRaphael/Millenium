<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Core;
use App\Core\Constants;
use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cart;
    private $em = null;

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        // TODO: Make constant and get them
        // TODO: Calculate subTotal
        $this -> initSession($request);
        $session = $request->getSession();
        $subtotal = $this -> cart ->getSubTotal();
        return $this->render('cart/cart.html.twig', [
            'cart' => $this->cart,
            'SubTotal' => $subtotal,
            'TVS' => $subtotal * (Constants::TVS),
            'TVQ' => $subtotal * (Constants::TVQ),
            'ShippingCost' => Constants::SHIPPING_COST
        ]);
    }
    #[Route('/cart/add/{idProduct}',name: 'cart_add')]
    public function addPurchase($idProduct,Request $request,ManagerRegistry $doctrine): Response
    {
        $this -> em = $doctrine -> getManager();
        $product = $this->em->getRepository(Product::class)->find($idProduct);

        $this -> initSession($request);
        $this -> cart->add($product,1,$product->getPrice());
        return $this-> redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{index}', name:'cart_delete')]
    public function deleteTodo($index, Request $request) : Response {
        $this->initSession($request);

        $this->cart->delete($index);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/update', name:'cart_update', methods:['POST'])]
    public function updateTodo(Request $request) : Response {
        $post = $request->request->all();
        $this->initSession($request);
        $action = $request->request->get('action');
        if($action == "update") {
            $this->cart->update($post);
        } 
        return $this->redirectToRoute('app_cart');
    }

    private function initSession(Request $request){
        $session = $request->getSession();
        $session -> set('name','Millenium');
        $this ->  cart = $session->get('purchases', new Cart());
        $session->set('purchases', $this->cart);
    }
}
