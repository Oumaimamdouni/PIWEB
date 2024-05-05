<?php

namespace App\Controller;
use App\Entity\LikeUser;
use App\Entity\Plat;
use App\Entity\CategorieMenu;
use App\Form\PlatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PlatRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
#[Route('/plat')]
class PlatController extends AbstractController
{

    #[Route('/categorie/{nomCategorie}/plats', name: 'app_plats_by_categorie', methods: ['GET'])]
    public function platsByCategorie(PlatRepository $platRepository, string $nomCategorie): Response
    {
        $plats = $platRepository->findByCategorie($nomCategorie);
    
        return $this->render('plat/index.html.twig', [
            'plats' => $plats,
        ]);
    }
    
    #[Route('/{idPlat}/like', name: 'like_plat', methods: ['POST'])]
    public function likePlat(int $idPlat, EntityManagerInterface $entityManager): Response
    {
        $plat = $entityManager->getRepository(Plat::class)->find($idPlat);

        if (!$plat) {
            return $this->redirectToRoute('app_categorie_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        $plat->setLikePlat($plat->getLikePlat() + 1);

        $likeUser = new LikeUser();
        $likeUser->setIdUser(0);
        $likeUser->setPlat($plat);
        $likeUser->setStatus('like');

        $entityManager->persist($plat);
        $entityManager->persist($likeUser);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorie_menu_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{idPlat}/dislike', name: 'dislike_plat', methods: ['POST'])]
    public function dislikePlat(int $idPlat, EntityManagerInterface $entityManager): Response
    {
        $plat = $entityManager->getRepository(Plat::class)->find($idPlat);

        if (!$plat) {
            return $this->redirectToRoute('app_categorie_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        $plat->setDislikePlat($plat->getDislikePlat() + 1);

        $likeUser = new LikeUser();
        $likeUser->setIdUser(0);
        $likeUser->setPlat($plat);
        $likeUser->setStatus('dislike');

        $entityManager->persist($plat);
        $entityManager->persist($likeUser);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorie_menu_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/', name: 'app_plat_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $plats = $entityManager
            ->getRepository(Plat::class)
            ->findAll();

        return $this->render('plat/index.html.twig', [
            'plats' => $plats,
        ]);
    }

    #[Route('/new', name: 'app_plat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plat);
            $entityManager->flush();

            return $this->redirectToRoute('app_plat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/{idPlat}', name: 'app_plat_show', methods: ['GET'])]
    public function show(Plat $plat, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new form for comments
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
    
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{idPlat}/comment', name: 'app_plat_comment', methods: ['POST'])]
    public function comment(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        // Create the comment entity
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Associate the comment with the plat
            $comment->setIdPlat($plat);
    
            // Persist and flush the new comment to the database
            $entityManager->persist($comment);
            $entityManager->flush();
    
            // Redirect back to the plat show page
            return $this->redirectToRoute('app_plat_index');
        }
    
        // If form is not valid, render the plat show page with form errors
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{idPlat}/edit', name: 'app_plat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_plat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/edit.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/{idPlat}', name: 'app_plat_delete', methods: ['POST'])]
    public function delete(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getIdPlat(), $request->request->get('_token'))) {
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plat_index', [], Response::HTTP_SEE_OTHER);
    }
}
