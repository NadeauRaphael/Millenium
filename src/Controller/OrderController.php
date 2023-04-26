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
        if($this->cart->getTotalPriceStripe() == 0.00){
            return $this->redirectToRoute('app_cart');
        }
        return $this->render('order/Review.html.twig', [
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

        return $this->render('order/index.html.twig', []);
    }

    #[Route('/stripe-success', name: 'stripe_success')]
    public function stripeSuccess(Request $request): Response
    {
        //Dans le TP 
        //Créer un commande
        //Transformer le panier en commande
        //MaJ des Quantité des produits
        //Vider le panier

        //Nous avons un paiement
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->initSession($request);
        $client = $this->getUser();
        try {
            $stripe = new \Stripe\StripeClient($_ENV["STRIPE_SECRET"]);
            $stripeSessionId = $request->query->get('stripe_id');
            $sessionStripe = $stripe->checkout->sessions->retrieve($stripeSessionId);
            // Payment intent to save in BD
            $paymentIntent = $sessionStripe->payment_intent;
            $order = new Order($paymentIntent, $client);
            foreach ($this->cart->getPurchases() as $purchase) {
                 //Merge Purchase
                 $order->addPurchase($this->em->merge($purchase));
                 $purchase->setProductQuantity();
            }

            $this->cart->empty();
            $this->em->persist($order);
            $this->em->flush();
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
        return $this->redirectToRoute('app_review');
    }

    // TODO: DONT HAVE TO MAKE THIS FUNCTION IN THREE CONTROLLER
    // Put in public to fix some problem i've encountered in the catalog page
    // Had to init the session in the catalog too
    public function initSession(Request $request)
    {
        $session = $request->getSession();
        $session->set('name', 'Millenium');
        $this->cart = $session->get('purchases', new Cart());
        $session->set('purchases', $this->cart);
    }
}
