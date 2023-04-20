<?php

namespace App\Controller;

use Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Cart;

class OrderController extends AbstractController
{
    private $cart;

    #[Route('/review', name: 'app_review')]
    public function review(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->initSession($request);
        return $this->render('order/Review.html.twig', [
            'cart' => $this->cart
        ]);
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Nous sommes connectés
        $user = $this->getUser();

        $successURL = $this->generateUrl('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . "?stripe_id={CHECKOUT_SESSION_ID}";

        return $this->render('order/index.html.twig', []);
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
