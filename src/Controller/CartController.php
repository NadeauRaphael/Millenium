<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Core;
use App\Core\Constants;
use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Core\Notification;
use App\Core\NotificationColor;

class CartController extends AbstractController
{
    private $cart;
    private $em = null;

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
 
        $this -> initSession($request);
        $session = $request->getSession();

        // Get the subtotal of the cart
        $subtotal = $this -> cart ->getSubTotal();

        // Check if subtotal is zero if it is then put the shipping cost to zero
        if($subtotal == 0){ $shippingCost = 0;}
        else{$shippingCost = Constants::SHIPPING_COST;}

        // Give all the info needed to display the total
        return $this->render('Cart/cart.html.twig', [
            'cart' => $this->cart,
            'SubTotal' => $subtotal,
            'TVS' => $subtotal * (Constants::TVS),
            'TVQ' => $subtotal * (Constants::TVQ),
            'ShippingCost' => $shippingCost
        ]);
    }
    #[Route('/cart/add/{idProduct}',name: 'cart_add')]
    public function addPurchase($idProduct,Request $request,ManagerRegistry $doctrine): Response
    {
        $this -> em = $doctrine -> getManager();
        $product = $this->em->getRepository(Product::class)->find($idProduct);
        $this -> initSession($request);
        $this -> cart->add($product);
        return $this-> redirectToRoute('app_cart');
    }

    #[Route('/cart/deleteAll', name:'cart_deleteAll')]
    public function deletePurchases(Request $request) : Response {
        $session = $request ->getSession();
        $session-> remove('purchases');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{index}', name:'cart_delete')]
    public function deletePurchase($index, Request $request) : Response {
        $this->initSession($request);

        $this->cart->delete($index);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/update', name:'cart_update')]
    public function updatePurchase(Request $request) : Response {
        $post = $request->request->all();
        $this->initSession($request);
        $action = $request->request->get('action');

        if($action == "update") {
            $updateState = $this->cart->update($post);
            if($updateState){
                $this->addFlash('update', 
                new Notification('success', 'Cart updated successfully', NotificationColor::INFO));
            }
            else{
                $this->addFlash('update', 
                new Notification('Error', 'Quantity entered is not a number', NotificationColor::DANGER));
            }
        } else if($action == "empty") {
            $session = $request->getSession();
            $session->remove('purchases');
        }
        $this->cart->update($post);
        return $this->redirectToRoute('app_cart');
    }

    // Put in public to fix some problem i've encountered in the catalog page
    // Had to init the session in the catalog too
    public function initSession(Request $request){
        $session = $request->getSession();
        $session -> set('name','Millenium');
        $this ->  cart = $session->get('purchases', new Cart());
        $session->set('purchases', $this->cart);
    }
}
