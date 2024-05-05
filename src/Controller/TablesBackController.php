<?php

namespace App\Controller;

use App\Entity\Tables;
use App\Form\Tables1Type;
use App\Form\TablesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tables/back')]
class TablesBackController extends AbstractController
{
    #[Route('/', name: 'app_tables_back_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tables = $entityManager
            ->getRepository(Tables::class)
            ->findAll();

        return $this->render('tables_back/index.html.twig', [
            'tables' => $tables,
        ]);
    }

    #[Route('/new', name: 'app_tables_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $table = new Tables();
        $form = $this->createForm(Tables1Type::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($table);
            $entityManager->flush();

            flash()->addSuccess('Your Table has been edited.');
            return $this->redirectToRoute('app_tables_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tables_back/new.html.twig', [
            'table' => $table,
            'form' => $form,
        ]);
    }

    #[Route('/{tableId}', name: 'app_tables_back_show', methods: ['GET'])]
    public function show(Tables $table): Response
    {
        return $this->render('tables_back/show.html.twig', [
            'table' => $table,
        ]);
    }

    #[Route('/{tableId}/edit', name: 'app_tables_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tables $table, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TablesType::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            flash()->addWarning('Your Table has been edited.');
            return $this->redirectToRoute('app_tables_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tables_back/edit.html.twig', [
            'table' => $table,
            'form' => $form,
        ]);
    }

    #[Route('/{tableId}', name: 'app_tables_back_delete', methods: ['POST'])]
    public function delete(Request $request, Tables $table, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$table->getTableId(), $request->request->get('_token'))) {
            flash()->addInfo('Your Table has been edited.');
            $entityManager->remove($table);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tables_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
