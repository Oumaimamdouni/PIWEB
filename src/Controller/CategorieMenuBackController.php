<?php

namespace App\Controller;

use App\Entity\CategorieMenu;
use App\Form\CategorieMenu1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie/menu/back')]
class CategorieMenuBackController extends AbstractController
{
    #[Route('/', name: 'app_categorie_menu_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categorieMenus = $entityManager
            ->getRepository(CategorieMenu::class)
            ->findAll();

        return $this->render('categorie_menu_back/index.html.twig', [
            'categorie_menus' => $categorieMenus,
        ]);
    }

    #[Route('/new', name: 'app_categorie_menu_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieMenu = new CategorieMenu();
        $form = $this->createForm(CategorieMenu1Type::class, $categorieMenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageCategorie')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $categorieMenu->setImageCategorie($newFilename);
            }

            $entityManager->persist($categorieMenu);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_menu_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie_menu_back/new.html.twig', [
            'categorie_menu' => $categorieMenu,
            'form' => $form,
        ]);
    }

    #[Route('/{idCategorie}', name: 'app_categorie_menu_back_show', methods: ['GET'])]
    public function show(CategorieMenu $categorieMenu): Response
    {
        return $this->render('categorie_menu_back/show.html.twig', [
            'categorie_menu' => $categorieMenu,
        ]);
    }

    #[Route('/{idCategorie}/edit', name: 'app_categorie_menu_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieMenu $categorieMenu, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(CategorieMenu1Type::class, $categorieMenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageCategorie')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $categorieMenu->setImageCategorie($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_menu_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie_menu_back/edit.html.twig', [
            'categorie_menu' => $categorieMenu,
            'form' => $form,
        ]);
    }

    #[Route('/{idCategorie}', name: 'app_categorie_menu_back_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieMenu $categorieMenu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieMenu->getIdCategorie(), $request->request->get('_token'))) {
            $entityManager->remove($categorieMenu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_menu_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
