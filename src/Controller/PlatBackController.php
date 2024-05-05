<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Repository\PlatRepository;
use App\Form\Plat1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Dompdf\Dompdf;
use Twig\Environment;

#[Route('/plat/back')]
class PlatBackController extends AbstractController
{
    #[Route('/', name: 'app_plat_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $plats = $entityManager
            ->getRepository(Plat::class)
            ->findAll();

        return $this->render('plat_back/index.html.twig', [
            'plats' => $plats,
        ]);
    }

    #[Route('/new', name: 'app_plat_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $plat = new Plat();
        $form = $this->createForm(Plat1Type::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Use Symfony's Slugger to generate a unique filename
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                // Move the file to the desired directory
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // Configure this parameter in services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }
                // Set the image filename to be saved in the database
                $plat->setImage($newFilename);
            }

            $entityManager->persist($plat);
            $entityManager->flush();

            return $this->redirectToRoute('app_plat_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat_back/new.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/{idPlat}', name: 'app_plat_back_show', methods: ['GET'])]
    public function show(Plat $plat): Response
    {
        return $this->render('plat_back/show.html.twig', [
            'plat' => $plat,
        ]);
    }

    #[Route('/{idPlat}/edit', name: 'app_plat_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plat $plat, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(Plat1Type::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Use Symfony's Slugger to generate a unique filename
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                // Move the file to the desired directory
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // Configure this parameter in services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }
                // Set the image filename to be saved in the database
                $plat->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_plat_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat_back/edit.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/export-pdf', name: 'export_pdf')]
    public function exportPdf(Environment $twig, PlatRepository $menuRepository): Response
    {
        // Fetch the menu data
        $menus = $menuRepository->findAll();

        // Render the PDF content with Twig
        $html = $twig->render('plat/pdf_template.html.twig', [
            'menus' => $menus,
        ]);

        // Initialize Dompdf and load the HTML content
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Return the PDF as a Response with appropriate headers
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="menus.pdf"');

        return $response;
    }

    #[Route('/{idPlat}', name: 'app_plat_back_delete', methods: ['POST'])]
    public function delete(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getIdPlat(), $request->request->get('_token'))) {
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plat_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
