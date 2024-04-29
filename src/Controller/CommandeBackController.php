<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\Commande1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use TCPDF;

#[Route('/commande/back')]
class CommandeBackController extends AbstractController
{
    #[Route('/', name: 'app_commande_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $commandes = $entityManager
            ->getRepository(Commande::class)
            ->findAll();
            $this->addFlash('info', 'Welcome to the Commande dashboard!');

        return $this->render('commande_back/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/export-pdf', name: 'app_commande_back_export_pdf', methods: ['GET'])]
    public function exportPdf(EntityManagerInterface $entityManager): Response
    {
        // Fetch the list of Commande entities
        $commandes = $entityManager->getRepository(Commande::class)->findAll();

        // Create a new TCPDF instance
        $pdf = new TCPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Company');
        $pdf->SetTitle('Commande List');
        $pdf->SetSubject('List of Commandes');
        $pdf->SetKeywords('TCPDF, PDF, Symfony, Commande');

        // Add a page
        $pdf->AddPage();

        // Set font and write content
        $pdf->SetFont('helvetica', '', 12);

        $pdf->Cell(0, 10, 'Commande List', 0, 1, 'C'); // Title

        // Table headers
        $pdf->Cell(30, 10, 'Numc', 1);
        $pdf->Cell(60, 10, 'Client Name', 1);
        $pdf->Cell(60, 10, 'Command Details', 1);
        $pdf->Cell(30, 10, 'Status', 1);
        $pdf->Ln(); // New line

        // Write data to the PDF
        foreach ($commandes as $commande) {
            $pdf->Cell(30, 10, $commande->getNumc(), 1);
            $pdf->Cell(60, 10, $commande->getAdressec(), 1);
            $pdf->Cell(60, 10, $commande->getPanier(), 1);
            $pdf->Cell(30, 10, $commande->getStatus(), 1);
            $pdf->Ln(); // New line after each row
        }
        $this->addFlash('info', 'Exporting Commande list to PDF.');

        // Output the PDF
        return new Response(
            $pdf->Output('commande_list.pdf', 'I'), // 'I' outputs inline, 'D' forces download
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    #[Route('/{numc}/change-status/{status}', name: 'app_commande_change_status')]
    public function changeStatus($numc, $status): RedirectResponse
    {
        $commande = $this->getDoctrine()->getRepository(Commande::class)->find($numc);
        $commande->setStatus($status);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $this->addFlash('success', 'Commande status changed.');

        return $this->redirectToRoute('app_commande_back_index');
    }

    #[Route('/new', name: 'app_commande_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();
            $this->addFlash('success', 'New Commande created successfully.');

            return $this->redirectToRoute('app_commande_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_back/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{numc}', name: 'app_commande_back_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {        

        return $this->render('commande_back/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{numc}/edit', name: 'app_commande_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('warning', 'Commande Edited successfully.');

            return $this->redirectToRoute('app_commande_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_back/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{numc}', name: 'app_commande_back_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {            
        $this->addFlash('warning', 'Commande deleted successfully.');

        if ($this->isCsrfTokenValid('delete'.$commande->getNumc(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
