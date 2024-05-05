<?php

namespace App\Controller;

use App\Entity\ReservationEvent;
use App\Entity\Evenement;
use App\Form\ReservationEventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/reservation/event')]
class ReservationEventController extends AbstractController
{
    #[Route('/', name: 'app_reservation_event_indexs', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reservationEvents = $entityManager
            ->getRepository(ReservationEvent::class)
            ->findAll();

        return $this->render('reservation_event/index.html.twig', [
            'reservation_events' => $reservationEvents,
        ]);
    }

    #[Route('/res', name: 'app_reser_home', methods: ['GET'])]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $reservationEvents = $entityManager
            ->getRepository(ReservationEvent::class)
            ->findAll();

        return $this->render('reservation_event/index.html.twig', [
            'reservation_events' => $reservationEvents,
        ]);
    }

    #[Route('/new', name: 'app_reservation_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eventId = $request->query->get('id');
        $reservationEvent = new ReservationEvent();
        $reservationEvent->setDate(new DateTime());
    
        if ($eventId) {
            $event = $entityManager->getRepository(Evenement::class)->find($eventId);
    
            if (!$event) {
                throw $this->createNotFoundException('Event not found');
            }
    
            $reservationEvent->setIdEvenement($event);
        }
    
        $form = $this->createForm(ReservationEventType::class, $reservationEvent);
        $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

                $twilioSid = 'AC0c09809cd373b25c13177fb51e73a17b';
                $twilioAuthToken = '3f2bc339643aa98fedf37421a0f1d61d';
                $twilioPhoneNumber = '+21629703092';
                $twilio = new \Twilio\Rest\Client($twilioSid, $twilioAuthToken);
                $userPhoneNumber = '+21629703092';
                $twilio->messages->create(
                    $userPhoneNumber,
                    [
                         "from" => '+12604403098',
                        "body" => "Confirmation Reservation"
                     ]
                );


            $entityManager->persist($reservationEvent);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('reservation_event/new.html.twig', [
            'reservation_event' => $reservationEvent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_event_show', methods: ['GET'])]
    public function show(ReservationEvent $reservationEvent): Response
    {
        return $this->render('reservation_event/show.html.twig', [
            'reservation_event' => $reservationEvent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReservationEvent $reservationEvent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationEventType::class, $reservationEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation_event/edit.html.twig', [
            'reservation_event' => $reservationEvent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_event_delete', methods: ['POST'])]
    public function delete(Request $request, ReservationEvent $reservationEvent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservationEvent->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservationEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
