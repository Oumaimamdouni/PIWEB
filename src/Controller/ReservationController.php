<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Zones;
use App\Entity\Tables;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager
            ->getRepository(Reservation::class)
            ->findAll();

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new/{zoneName}/{tableId}', name: 'app_reservation_zone_new', methods: ['GET', 'POST'])]
    public function newRes(SessionInterface $session,Request $request, EntityManagerInterface $entityManager, string $zoneName, int $tableId): Response
    {
        $zone = $entityManager->getRepository(Zones::class)->findOneBy(['nom' => $zoneName]);
        if (!$zone) {
            $zone = new Zones();
            $zone->setNom($zoneName);
            $entityManager->persist($zone);
        }
    
        $reservation = new Reservation();
        $table = $entityManager->getRepository(Tables::class)->find($tableId);
        $reservation->setTableId($table);
        $reservation->setZone($zone);
        $reservation->setDater(new \DateTime());
        $reservation->setIdC($session->get("user_id"));
        // Create and handle the form
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->sendEmail("Eya.Benslimen@esprit.tn", "Confirmation", "Votre Reservation est confirmé");
            $entityManager->persist($reservation);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(SessionInterface $session,Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        $reservation->setIdC($session->get("user_id"));

        if ($form->isSubmitted() && $form->isValid()) {
            $this->sendEmail("Eya.Benslimen@esprit.tn", "Confirmation", "Votre Reservation est confirmé");
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    function sendEmail($recipientEmail, $subject, $message)
    {
        $transport = new EsmtpTransport('smtp.gmail.com', 587);
        $transport->setUsername("mehergames29@gmail.com");
        $transport->setPassword("nszgqaynqpetuucj");
    
        $mailer = new Mailer($transport);
    
        $email = (new Email())
            ->from("pidev@gmail.com")
            ->to($recipientEmail)
            ->subject($subject)
            ->text($message);
    
        $mailer->send($email);
    }

    #[Route('/{idR}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{idR}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(SessionInterface $session,Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setIdC($session->get("user_id"));
            $entityManager->flush();
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{idR}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getIdR(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
