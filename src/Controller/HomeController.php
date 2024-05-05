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
    
    $commandeCount = $entityManager->getRepository(\App\Entity\Commande::class)->count([]);
    
    $restoCount = $entityManager->getRepository(\App\Entity\Resto::class)->count([]);
    $reservationCount = $entityManager->createQuery('SELECT COUNT(r.idR) FROM App\Entity\Reservation r')->getSingleScalarResult();
    $zoneCount = $entityManager->createQuery('SELECT COUNT(z.zoneId) FROM App\Entity\Zones z')->getSingleScalarResult();
    
    $tableCount = $entityManager->createQuery('SELECT COUNT(t.tableId) FROM App\Entity\Tables t')->getSingleScalarResult();

    $totalPrice = $entityManager->createQuery(
        'SELECT SUM(c.price) FROM App\Entity\Commande c'
    )->getSingleScalarResult();

    $statusCount = $entityManager->createQuery(
        'SELECT c.status, COUNT(c) as count FROM App\Entity\Commande c GROUP BY c.status'
    )->getResult();

    return $this->render('home/indexBack.html.twig', [
        'reservation_count ' => 10,
        'commande_count' => $commandeCount,
        'resto_count' => $restoCount,
        'total_price' => $totalPrice,
        'status_count' => $statusCount,
        'table_count' => $tableCount,
        'zone_count' => $zoneCount,
    ]);
  }
}
