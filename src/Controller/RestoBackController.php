<?php

namespace App\Controller;

use App\Entity\Resto;
use App\Form\Resto1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

#[Route('/resto/back')]
class RestoBackController extends AbstractController
{

    #[Route('/', name: 'app_resto_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $restos = $entityManager
            ->getRepository(Resto::class)
            ->findAll();
        
            $this->addFlash('success', 'Welcome to the Resto dashboard!');

        return $this->render('resto_back/index.html.twig', [
            'restos' => $restos,
        ]);
    }


    #[Route('/new', name: 'app_resto_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resto = new Resto();
        $form = $this->createForm(Resto1Type::class, $resto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($resto);
            $entityManager->flush();
            $this->addFlash('success', 'New Resto created successfully!');

            return $this->redirectToRoute('app_resto_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resto_back/new.html.twig', [
            'resto' => $resto,
            'form' => $form,
        ]);
    }
    
        #[Route('/export-excel', name: 'app_resto_back_export_excel', methods: ['GET'])]
        public function exportExcel(EntityManagerInterface $entityManager): Response
        {
            // Fetch the list of Resto entities
            $restos = $entityManager->getRepository(Resto::class)->findAll();
    
            // Create a new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
            // Set headers
            $headers = ['IdResto', 'Nomr', 'Adresser', 'Numr'];
            foreach ($headers as $index => $header) {
                $column = chr(65 + $index); // 65 is ASCII for 'A'
                $sheet->setCellValue("{$column}1", $header);
            }
    
            // Fill in the data
            $row = 2;
            foreach ($restos as $resto) {
                $sheet->setCellValue("A{$row}", $resto->getIdResto());
                $sheet->setCellValue("B{$row}", $resto->getNomr());
                $sheet->setCellValue("C{$row}", $resto->getAdresser());
                $sheet->setCellValue("D{$row}", $resto->getNumr());
                $row++;
            }
    
            // Write the spreadsheet to a temporary file
            $writer = new Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'resto_excel');
            $writer->save($tempFile);
            $this->addFlash('info', 'Exporting Resto list to Excel.');

            // Return the Excel file as a response
            return new Response(
                file_get_contents($tempFile),
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="resto_list.xlsx"',
                ]
            );
        }
    

    #[Route('/resto/back/export-pdf', name: 'app_resto_back_export_pdf', methods: ['GET'])]
    public function exportPdf(EntityManagerInterface $entityManager): Response
    {
        try {
            // Fetch all Restos
            $restos = $entityManager->getRepository(Resto::class)->findAll();

            if (empty($restos)) {
                throw new EntityNotFoundException("No restos found to export.");
            }

            // Generate the PDF with the fetched data
            $dompdf = new Dompdf();
            $html = $this->renderView('resto_back/pdf.html.twig', ['restos' => $restos]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return new Response(
                $dompdf->output(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="resto_list.pdf"',
                ]
            );

        } catch (EntityNotFoundException $e) {
            // Handle the case where no restos are found
            return new Response(
                "Error: " . $e->getMessage(),
                404,
                ['Content-Type' => 'text/plain']
            );

        } catch (Exception $e) {
            // General exception handling
            return new Response(
                "An error occurred while generating the PDF: " . $e->getMessage(),
                500,
                ['Content-Type' => 'text/plain']
            );
        }
    }

    #[Route('/{idResto}', name: 'app_resto_back_show', methods: ['GET'])]
    public function show(Resto $resto): Response
    {        
        $this->addFlash('info', 'Showing Resto details.');

        return $this->render('resto_back/show.html.twig', [
            'resto' => $resto,
        ]);
    }

    #[Route('/{idResto}/edit', name: 'app_resto_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Resto $resto, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Resto1Type::class, $resto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('warning', 'Resto Edited successfully.');

            return $this->redirectToRoute('app_resto_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resto_back/edit.html.twig', [
            'resto' => $resto,
            'form' => $form,
        ]);
    }



    #[Route('/{idResto}', name: 'app_resto_back_delete', methods: ['POST'])]
    public function delete(Request $request, Resto $resto, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('warning', 'Resto deleted successfully.');

        if ($this->isCsrfTokenValid('delete'.$resto->getIdResto(), $request->request->get('_token'))) {
            $entityManager->remove($resto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_resto_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
