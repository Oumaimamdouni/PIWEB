<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
  #[Route('/home', name: 'app_home')]
  public function index(): Response
  {
    return $this->render('home/index.html.twig', [
      'controller_name' => 'HomeController',
    ]);
  }
  #[Route('/back', name: 'app_homeBack')]
  public function indexBack(EntityManagerInterface $entityManager): Response
  {
    // Fetch similar statistics as above
    $commandeCount = $entityManager->getRepository(\App\Entity\Commande::class)->count([]);
    $restoCount = $entityManager->getRepository(\App\Entity\Resto::class)->count([]);

    $totalPrice = $entityManager->createQuery(
        'SELECT SUM(c.price) FROM App\Entity\Commande c'
    )->getSingleScalarResult();

    $statusCount = $entityManager->createQuery(
        'SELECT c.status, COUNT(c) as count FROM App\Entity\Commande c GROUP BY c.status'
    )->getResult();

    return $this->render('home/indexBack.html.twig', [
        'controller_name' => 'HomeController',
        'commande_count' => $commandeCount,
        'resto_count' => $restoCount,
        'total_price' => $totalPrice,
        'status_count' => $statusCount,
    ]);
  }
}
