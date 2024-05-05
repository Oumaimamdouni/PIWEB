<?php

namespace App\Controller;

use App\Entity\ReservationEvent;
use App\Form\ReservationEvent1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/reservation/event/back')]
class ReservationEventBackController extends AbstractController
{
    #[Route('/', name: 'app_reservation_event_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reservationEvents = $entityManager
            ->getRepository(ReservationEvent::class)
            ->findAll();

        return $this->render('reservation_event_back/index.html.twig', [
            'reservation_events' => $reservationEvents,
        ]);
    }

    #[Route('/new', name: 'app_reservation_event_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservationEvent = new ReservationEvent();
        $reservationEvent->setDate(new DateTime());

        $form = $this->createForm(ReservationEvent1Type::class, $reservationEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservationEvent);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_event_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation_event_back/new.html.twig', [
            'reservation_event' => $reservationEvent,
            'form' => $form,
        ]);
    }

    #[Route('/export/pdf', name: 'app_reservation_event_back_export_pdf', methods: ['GET'])]
    public function exportToPdf(EntityManagerInterface $entityManager): Response
    {
        // Get all reservation events
        $reservationEvents = $entityManager
            ->getRepository(ReservationEvent::class)
            ->findAll();

        // Initialize DOMPDF with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // Build HTML content for the PDF
        $html = '<h1>Reservation Events</h1>';
        foreach ($reservationEvents as $event) {
            $html .= sprintf(
                '<p>Event: %s | Date: %s</p>',
                $event->getDescription(),
                $event->getDate()->format('Y-m-d')
            );
        }

        // Load HTML content into DOMPDF
        $dompdf->loadHtml($html);

        // Render the HTML as PDF
        $dompdf->render();

        // Return the generated PDF as a download
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="reservation_events.pdf"',
            ]
        );
    }

    #[Route('/{id}', name: 'app_reservation_event_back_show', methods: ['GET'])]
    public function show(ReservationEvent $reservationEvent): Response
    {
        return $this->render('reservation_event_back/show.html.twig', [
            'reservation_event' => $reservationEvent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_event_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReservationEvent $reservationEvent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationEvent1Type::class, $reservationEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_event_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation_event_back/edit.html.twig', [
            'reservation_event' => $reservationEvent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_event_back_delete', methods: ['POST'])]
    public function delete(Request $request, ReservationEvent $reservationEvent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservationEvent->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservationEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_event_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
