<?php

namespace App\Controller;

use App\Entity\Zones;
use App\Form\ZonesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Flasher\Toastr\Prime\ToastrFactory;

#[Route('/zones')]
class ZonesController extends AbstractController
{
    #[Route('/', name: 'app_zones_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $zones = $entityManager
            ->getRepository(Zones::class)
            ->findAll();

        return $this->render('zones/index.html.twig', [
            'zones' => $zones,
        ]);
    }

    #[Route('/new', name: 'app_zones_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $zone = new Zones();
        $form = $this->createForm(ZonesType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zone);
            $entityManager->flush();
            $flasher->success('Entity created successfully!')->priority(1);

            return $this->redirectToRoute('app_zones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('zones/new.html.twig', [
            'zone' => $zone,
            'form' => $form,
        ]);
    }

    #[Route('/{zoneId}', name: 'app_zones_show', methods: ['GET'])]
    public function show(Zones $zone): Response
    {
        return $this->render('zones/show.html.twig', [
            'zone' => $zone,
        ]);
    }

    #[Route('/{zoneId}/edit', name: 'app_zones_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Zones $zone, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ZonesType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_zones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('zones/edit.html.twig', [
            'zone' => $zone,
            'form' => $form,
        ]);
    }

    #[Route('/{zoneId}', name: 'app_zones_delete', methods: ['POST'])]
    public function delete(Request $request, Zones $zone, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$zone->getZoneId(), $request->request->get('_token'))) {
            $entityManager->remove($zone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_zones_index', [], Response::HTTP_SEE_OTHER);
    }
}
