<?php

namespace App\Controller;

use App\Core\Notification;
use App\Core\NotificationColor;
use Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{
    private $em = null;
    private $cart;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/review', name: 'app_review')]
    public function review(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->initSession($request);
        if($this->cart->isEmpty()){
            return $this->redirectToRoute('app_cart');
        }
        return $this->render('Order/Review.html.twig', [
            'cart' => $this->cart
        ]);
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Nous sommes connectés
        $user = $this->getUser();
        $this->initSession($request);
        $successURL = $this->generateUrl('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . "?stripe_id={CHECKOUT_SESSION_ID}";
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $sessionData = [
            'line_items' => [[
                'quantity' => 1,
                'price_data' => ['unit_amount' => $this->cart->getTotalPriceStripe(), 'currency' => 'CAD', 'product_data' => ['name' => 'Millennium']]
            ]],
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'success_url' => $successURL,
            'cancel_url' => $this->generateUrl('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ];

        // Extension curl nécessaire
        $checkoutSession = \Stripe\Checkout\Session::create($sessionData);
        return $this->redirect($checkoutSession->url, 303);

        return $this->render('Order/index.html.twig', []);
    }

    #[Route('/stripe-success', name: 'stripe_success')]
    public function stripeSuccess(Request $request): Response
    {
        //Paiment
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->initSession($request);
        $client = $this->getUser();
        $msgErr="";
        try {
            $stripe = new \Stripe\StripeClient($_ENV["STRIPE_SECRET"]);
            $stripeSessionId = $request->query->get('stripe_id');
            $sessionStripe = $stripe->checkout->sessions->retrieve($stripeSessionId);
            // Payment intent to save in BD
            $paymentIntent = $sessionStripe->payment_intent;
            $order = new Order($paymentIntent, $client,$this->cart);
            foreach ($order->getPurchases() as $purchase) {
                 //Merge Purchase
                 $newProduct = $this->em->merge($purchase->getProduct());

                 if(!($newProduct->Sold($purchase->getQuantity()))){
                    $msgErr .= 'Product '.$newProduct->getName().' is out of stock <br>';
                 }
                 $purchase->setProduct($newProduct);
                 $order->addPurchase($purchase);   
            }
            $order->inPreparation();
            $this->em->persist($order);
            $this->em->flush();
            $this->cart->empty();

            // Product out of stock
            if($msgErr != ""){
                $this->addFlash(
                    'update',
                    new Notification('Out of stock', $msgErr . 'The order number ' . $order->getIdOrder(). " will be delivered as soon as the product come back in stock" , NotificationColor::INFO)
                ); 
            }
            else{
                $this->addFlash(
                    'update',
                    new Notification('Success', "The order ".$order->getIdOrder()." was proceded, will be delivered soon" , NotificationColor::SUCCESS)
                ); 
            }
        } catch (\Exception $e) {
            $this->addFlash(
                'update',
                new Notification('Error', 'Error with the order', NotificationColor::DANGER)
            );
            return $this->redirectToRoute('app_cart');
        }
        return $this->redirectToRoute('app_order',['idOrder' => $order->getIdOrder()]);
    }

    #[Route('/stripe-cancel', name: 'stripe_cancel')]
    public function stripeCancel(): Response
    {
        $this->addFlash(
            'update',
            new Notification('Error', 'Error with the order, please try again.', NotificationColor::DANGER)
        );
        return $this->redirectToRoute('app_cart');
    }
    
    public function initSession(Request $request)
    {
        $session = $request->getSession();
        $session->set('name', 'Millenium');
        $this->cart = $session->get('purchases', new Cart());
        $session->set('purchases', $this->cart);
    }
}
