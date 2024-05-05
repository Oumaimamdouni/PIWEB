<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\Utilisateurs1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/utilisateurs/back')]
class UtilisateursBackController extends AbstractController
{
    private SessionInterface $session; 

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    #[Route('/', name: 'app_utilisateurs_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $redirect = $this->checkAdmin($this->session);
        $utilisateurs = $entityManager
            ->getRepository(Utilisateurs::class)
            ->findAll();

        return $this->render('utilisateurs_back/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    public function generatePDF(string $htmlContent, string $fileName): Response
    {
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->writeHTML($htmlContent);

        $response = new Response($pdf->Output($fileName, 'I'));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="'.$fileName.'"');

        return $response;
    }

    #[Route('/{id}/block', name: 'app_utilisateurs_back_block', methods: ['GET'])]
    public function blockUser(Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $this->checkAdmin($this->session);
        $utilisateur->setBlocked(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_utilisateurs_back_index');
    }

    #[Route('/users/export/pdf', name: 'user_export_pdf')]
    public function exportToPDF(EntityManagerInterface $entityManager): Response
    {
        $this->checkAdmin($this->session);
        $utilisateurs = $entityManager->getRepository(Utilisateurs::class)->findAll();

        $html = $this->renderView('utilisateurs_back/pdf_template.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
        return $this->generatePDF($html, 'utilisateurs_export.pdf');
    }

    #[Route('/users/export/excel', name: 'user_export_excel')]
    public function exportToExcel(EntityManagerInterface $entityManager): StreamedResponse
    {
        $this->checkAdmin($this->session);
        // Retrieve all users
        $utilisateurs = $entityManager->getRepository(Utilisateurs::class)->findAll();
    
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Utilisateurs');
    
        // Set the headers for the Excel sheet
        $headers = ['Id', 'Nom', 'Prenom', 'Email', 'Role', 'Blocked'];
        $sheet->fromArray($headers, NULL, 'A1');
    
        // Add user data to the Excel sheet
        $rowNum = 2; // Starting from the second row
        foreach ($utilisateurs as $utilisateur) {
            $sheet->fromArray([
                $utilisateur->getId(),
                $utilisateur->getNom(),
                $utilisateur->getPrenom(),
                $utilisateur->getEmail(),
                $utilisateur->getRole(),
                $utilisateur->isBlocked() ? 'Yes' : 'No',
            ], NULL, "A$rowNum");
            $rowNum++;
        }
    
        // Create a streamed response to return the Excel file
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
    
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="users.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
    
        return $response;
    }


    #[Route('/{id}/unblock', name: 'app_utilisateurs_back_unblock', methods: ['GET'])]
    public function unblockUser(Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $this->checkAdmin($this->session);
        $utilisateur->setBlocked(false);
        $entityManager->flush();

        return $this->redirectToRoute('app_utilisateurs_back_index');
    }

    #[Route('/new', name: 'app_utilisateurs_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->checkAdmin($this->session);
        $utilisateur = new Utilisateurs();
        $form = $this->createForm(Utilisateurs1Type::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Use MD5 encryption for password
            $password = $form->get('motDePasse')->getData();
            $encodedPassword = md5($password);
            $utilisateur->setMotDePasse($encodedPassword);
    
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
                    // Handle error
                }
    
                $utilisateur->setImage($newFilename);
            }
            $entityManager->persist($utilisateur);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_utilisateurs_back_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('utilisateurs_back/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateurs_back_show', methods: ['GET'])]
    public function show(Utilisateurs $utilisateur): Response
    {
        $this->checkAdmin($this->session);
        return $this->render('utilisateurs_back/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateurs_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $this->checkAdmin($this->session);
        $form = $this->createForm(Utilisateurs1Type::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if password field is not empty
            $password = $form->get('motDePasse')->getData();
            if (!empty($password)) {
                // Use MD5 encryption for password
                $encodedPassword = md5($password);
                $utilisateur->setMotDePasse($encodedPassword);
            }
    
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
                    // Handle error
                }
    
                $utilisateur->setImage($newFilename);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_utilisateurs_back_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('utilisateurs_back/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateurs_back_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $this->checkAdmin($this->session);
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateurs_back_index', [], Response::HTTP_SEE_OTHER);
    }

    public function checkAdmin(SessionInterface $session): void
    {
        $user = $session->get('user');
        if ($user == null || strtolower($user->getRole()) != 'admin') {
            throw new AccessDeniedException('Access denied: Admins only');
        }
    }

}
