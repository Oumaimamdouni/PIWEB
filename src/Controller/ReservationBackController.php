<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\Reservation1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Flasher\Toastr\Prime\ToastrFactory;
use DateTime;

#[Route('/reservation/back')]
class ReservationBackController extends AbstractController
{
    #[Route('/', name: 'app_reservation_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager
            ->getRepository(Reservation::class)
            ->findAll();

        return $this->render('reservation_back/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{idR}/change-status/{status}', name: 'app_reservation_back_change_status', methods: ['GET'])]
    public function changeStatus(int $idR, string $status, EntityManagerInterface $entityManager): Response
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($idR);
    
        if (!$reservation) {
            return new Response(['message' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
        }
    
        flash()->addWarning('Status have been changed.');
        $reservation->setStatus($status);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_reservation_back_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_reservation_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $reservation->setDater(new DateTime());
        $form = $this->createForm(Reservation1Type::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
            
            flash()->addSuccess('Reservation Created.');
            return $this->redirectToRoute('app_reservation_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation_back/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/reservation/export-excel', name: 'app_reservation_export_excel')]
    public function exportToExcel(EntityManagerInterface $entityManager): Response
    {
        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header row
        $sheet->setCellValue('A1', 'IdR');
        $sheet->setCellValue('B1', 'IdC');
        $sheet->setCellValue('C1', 'Zone');
        $sheet->setCellValue('D1', 'Date');
        $sheet->setCellValue('E1', 'TableId');
        $sheet->setCellValue('F1', 'Status');
        
        // Fetch reservation data using the repository
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();
        
        // Populate the spreadsheet with reservation data
        $row = 2;
        foreach ($reservations as $reservation) {
            $sheet->setCellValue('A' . $row, $reservation->getIdR());
            $sheet->setCellValue('B' . $row, $reservation->getIdC());
            $sheet->setCellValue('C' . $row, $reservation->getZone());
            $sheet->setCellValue('D' . $row, $reservation->getDater()->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $reservation->getTableId());
            $sheet->setCellValue('F' . $row, $reservation->getStatus());
            $row++;
        }
        
        // Save the spreadsheet to a temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'res_excel_');
        $writer->save($tempFile);
        
        // Return the Excel file as a response
        return new Response(
            file_get_contents($tempFile),
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="reservation_data.xlsx"',
            ]
        );
    }
    #[Route('/{idR}', name: 'app_reservation_back_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation_back/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{idR}/edit', name: 'app_reservation_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Reservation1Type::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            flash()->addWarning('Reservation Edited');
            return $this->redirectToRoute('app_reservation_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation_back/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{idR}', name: 'app_reservation_back_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getIdR(), $request->request->get('_token'))) {
            flash()->addInfo('Reservation Deleted.');
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
