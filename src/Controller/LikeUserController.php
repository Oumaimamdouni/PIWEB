<?php

namespace App\Controller;

use App\Entity\LikeUser;
use App\Form\LikeUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/like/user')]
class LikeUserController extends AbstractController
{
    #[Route('/', name: 'app_like_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $likeUsers = $entityManager
            ->getRepository(LikeUser::class)
            ->findAll();

        return $this->render('like_user/index.html.twig', [
            'like_users' => $likeUsers,
        ]);
    }

    #[Route('/new', name: 'app_like_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $likeUser = new LikeUser();
        $form = $this->createForm(LikeUserType::class, $likeUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($likeUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_like_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('like_user/new.html.twig', [
            'like_user' => $likeUser,
            'form' => $form,
        ]);
    }

    #[Route('/{idLike}', name: 'app_like_user_show', methods: ['GET'])]
    public function show(LikeUser $likeUser): Response
    {
        return $this->render('like_user/show.html.twig', [
            'like_user' => $likeUser,
        ]);
    }

    #[Route('/{idLike}/edit', name: 'app_like_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LikeUser $likeUser, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LikeUserType::class, $likeUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_like_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('like_user/edit.html.twig', [
            'like_user' => $likeUser,
            'form' => $form,
        ]);
    }

    #[Route('/{idLike}', name: 'app_like_user_delete', methods: ['POST'])]
    public function delete(Request $request, LikeUser $likeUser, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$likeUser->getIdLike(), $request->request->get('_token'))) {
            $entityManager->remove($likeUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_like_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
