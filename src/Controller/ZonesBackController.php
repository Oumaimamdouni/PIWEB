<?php

namespace App\Controller;

use App\Entity\Zones;
use App\Form\Zones1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

#[Route('/zones/back')]
class ZonesBackController extends AbstractController
{
    #[Route('/', name: 'app_zones_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $zones = $entityManager
            ->getRepository(Zones::class)
            ->findAll();

        return $this->render('zones_back/index.html.twig', [
            'zones' => $zones,
        ]);
    }

    #[Route('/new', name: 'app_zones_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $zone = new Zones();
        $form = $this->createForm(Zones1Type::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $uploadsDirectory = $this->getParameter('uploads_directory');
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the uploads directory
                $imageFile->move(
                    $uploadsDirectory,
                    $newFilename
                );

                // Update the 'image' property of the zone entity with the filename
                $zone->setImage($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($zone);
            $entityManager->flush();
            flash()->addSuccess('Your Zones has been created.');

            return $this->redirectToRoute('app_zones_back_index');
        }

        return $this->render('zones_back/new.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{zoneId}', name: 'app_zones_back_show', methods: ['GET'])]
    public function show(Zones $zone): Response
    {
        return $this->render('zones_back/show.html.twig', [
            'zone' => $zone,
        ]);
    }

    #[Route('/{zoneId}/edit', name: 'app_zones_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Zones $zone, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Zones1Type::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            flash()->addWarning('Your Zones has been edited.');

            return $this->redirectToRoute('app_zones_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('zones_back/edit.html.twig', [
            'zone' => $zone,
            'form' => $form,
        ]);
    }

    #[Route('/{zoneId}', name: 'app_zones_back_delete', methods: ['POST'])]
    public function delete(Request $request, Zones $zone, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$zone->getZoneId(), $request->request->get('_token'))) {
            
            flash()->addInfo('Your Zones has been Deleted.');
            $entityManager->remove($zone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_zones_back_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/zones/export/pdf', name: 'app_zones_export_pdf')]
    public function exportPdf(): Response
    {
        $zones = $this->getDoctrine()->getRepository(Zones::class)->findAll();

        // Initialize TCPDF (or another PDF generator)
        $pdf = new TCPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Zones Export");

        // Set margins and other settings
        $pdf->SetMargins(15, 10, 15);
        $pdf->SetAutoPageBreak(true, 10);

        // Add a page
        $pdf->AddPage();

        // Add content to the PDF
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'List of Zones', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Ln(10);

        // Table headers
        $pdf->Cell(30, 10, 'Zone ID', 1);
        $pdf->Cell(60, 10, 'Nom', 1);
        $pdf->Cell(60, 10, 'Description', 1);
        $pdf->Cell(40, 10, 'Capacity', 1);
        $pdf->Ln();

        // Table content
        foreach ($zones as $zone) {
            $pdf->Cell(30, 10, $zone->getZoneId(), 1);
            $pdf->Cell(60, 10, $zone->getNom(), 1);
            $pdf->Cell(60, 10, $zone->getDescription(), 1);
            $pdf->Cell(40, 10, $zone->getCapacity(), 1);
            $pdf->Ln();
        }

        // Output PDF as download
        $pdf->Output('zones_export.pdf', 'D');

        // The response can return early because the PDF is sent directly
        return new Response();
    }
}
