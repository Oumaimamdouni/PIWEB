<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Resto;
use App\Entity\Plat;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $commandes = $entityManager
            ->getRepository(Commande::class)
            ->findAll();

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Check for the Resto ID from the query parameter
        $idResto = $request->query->get('idResto');
        if (!$idResto) {
            throw $this->createNotFoundException('idResto parameter is missing or empty');
        }

        // Find the Resto by ID
        $resto = $entityManager->getRepository(Resto::class)->find($idResto);
        if (!$resto) {
            throw $this->createNotFoundException('Resto not found');
        }

        // Create the form without associating it with a Commande
        $form = $this->createForm(CommandeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande = new Commande();
            $commande->setIdResto($resto);
            $selectedPlats = $form->get('panier')->getData();
            $adresseC = $form->get('adressec')->getData();
            $serializedPanier = json_encode(array_map(function (Plat $plat) {
                return $plat->getNomPlat();
            }, $selectedPlats));
            $totalPrice = array_reduce($selectedPlats, function ($sum, Plat $plat) {
                return $sum + $plat->getPrix();
            }, 0);
            $commande->setPanier($serializedPanier);
            $commande->setPrice($totalPrice);
            $commande->setAdressec($adresseC);
            $entityManager->persist($commande);
            $entityManager->flush();
            $session->set('commande', $commande);
            return $this->redirectToRoute('app_payment');
        }

        // Render the form if it is not submitted or is invalid
        return $this->renderForm('commande/new.html.twig', [
            'form' => $form,
        ]);
    }
    

    #[Route('/{numc}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{numc}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{numc}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getNumc(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
