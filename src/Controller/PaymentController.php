<?php

namespace App\Controller;

use PHPUnit\TextUI\Command;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Stripe\Stripe;
use Stripe\Price;
use Stripe\Product;

class PaymentController extends AbstractController
{

    private $commandeC;

    #[Route('/payment', name: 'app_payment')]
    public function index(Request $request,EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $commande = new Commande();
        $commandeC = $session->get('commande',$commande);

        $stripeSecretKey = "sk_test_51P9DwELvVIpdSeCZRNChwoPzZnj0z940QapxHRtmlb4yQTw5g4DF1vAoObInYzgmSvuW7KK50qlVNvPpAPgzSBVs00K9lKE8Kt";
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        $DOMAIN = 'http://localhost:8000';
    
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price' => $this->getPrice("Ticket Purchase", $commandeC->getPrice() * 100),
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $DOMAIN . '/success',
            'cancel_url' => $DOMAIN . '/fail',
        ]);
    
        return $this->redirect($checkout_session->url);
    }

    public function getPrice($productName,$price):string{
        Stripe::setApiKey('sk_test_51P9DwELvVIpdSeCZRNChwoPzZnj0z940QapxHRtmlb4yQTw5g4DF1vAoObInYzgmSvuW7KK50qlVNvPpAPgzSBVs00K9lKE8Kt');
        $product = Product::create([
            'name' => $productName,
        ]);
        $price = Price::create([
            'unit_amount' => $price,
            'currency' => 'usd',
            'product' => $product,
        ]);
        return $price->id;

    }

    #[Route('/success', name: 'suc_payment')]
    public function success(Request $request,EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
    
        return $this->render('commande/success.html.twig');
    }

    #[Route('/fail', name: 'fail_payment')]
    public function fail(Request $request,EntityManagerInterface $entityManager): Response
    {
        return $this->render('commande/fail.html.twig');
    }
}
