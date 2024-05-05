<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\Evenement1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use DateTime;

#[Route('/evenement/back')]
class EvenementBackController extends AbstractController
{
    #[Route('/', name: 'app_evenement_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager
            ->getRepository(Evenement::class)
            ->findAll();

        return $this->render('evenement_back/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/export/excel', name: 'export_excel', methods: ['GET'])]
    public function exportExcel(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Ensure session is started before doing any output
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        // Create the spreadsheet and define headers
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Event Name');
        $sheet->setCellValue('B1', 'Start Date');
        $sheet->setCellValue('C1', 'End Date');
        $sheet->setCellValue('D1', 'Participants'); // New column for number of participants
        $sheet->setCellValue('E1', 'Duration (days)'); // New column for event duration
    
        // Retrieve events from the database
        $evenements = $entityManager->getRepository(Evenement::class)->findAll();
    
        $totalParticipants = 0;
        $totalDuration = 0;
        $row = 2; // Start from the second row
    
        foreach ($evenements as $evenement) {
            $eventName = $evenement->getNom();
            $startDate = $evenement->getDatedebut()->format('Y-m-d');
            $endDate = $evenement->getDatefin()->format('Y-m-d');
            $participants = $evenement->getNbparticipant(); // Number of participants
            $duration = $evenement->getDatefin()->diff($evenement->getDatedebut())->days; // Duration in days
    
            // Fill the row with event data
            $sheet->setCellValue('A' . $row, $eventName);
            $sheet->setCellValue('B' . $row, $startDate);
            $sheet->setCellValue('C' . $row, $endDate);
            $sheet->setCellValue('D' . $row, $participants);
            $sheet->setCellValue('E' . $row, $duration);
    
            // Update totals for statistics
            $totalParticipants += $participants;
            $totalDuration += $duration;
            $row++;
        }
    
        // Add a summary row at the end with total statistics
        $summaryRow = $row + 1; // Add the summary below the last data row
        $sheet->setCellValue('A' . $summaryRow, 'Total Events');
        $sheet->setCellValue('B' . $summaryRow, count($evenements)); // Total number of events
        $sheet->setCellValue('D' . $summaryRow, $totalParticipants); // Total number of participants
        $sheet->setCellValue('E' . $summaryRow, $totalDuration); // Total duration of all events (in days)
    
        // Output the spreadsheet to response
        $writer = new Xlsx($spreadsheet);
    
        ob_start(); // Start output buffering
        $writer->save('php://output'); // Write to output buffer
        $excelOutput = ob_get_clean(); // Get buffered output
    
        // Create response with correct headers
        $response = new Response($excelOutput);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="events.xlsx"');
    
        return $response;
    }

    #[Route('/new', name: 'app_evenement_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        
        $evenement->setDatedebut(new DateTime());
        $evenement->setDatefin(new DateTime());

        $form = $this->createForm(Evenement1Type::class, $evenement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }
                $evenement->setImage($newFilename);
            }
    
            $entityManager->persist($evenement);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_evenement_back_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('evenement_back/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_back_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement_back/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Evenement1Type::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }
                $evenement->setImage($newFilename);
            }


            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement_back/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_back_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
