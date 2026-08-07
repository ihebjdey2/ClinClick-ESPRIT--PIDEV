<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CategoryR;
use App\Form\CategoryRType;
use App\Repository\CategoryRRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/RDV')]
final class CategoryRController extends AbstractController
{
    #[Route('/', name: 'app_categoryR_index', methods: ['GET'])]
    public function index(CategoryRRepository $repository): Response
    {
        $categories = $repository->findAllWithAppointments();
        $maximumAppointments = 0;
        foreach ($categories as $category) {
            $maximumAppointments = max($maximumAppointments, $category->getRDVs()->count());
        }

        return $this->render('category_r/index.html.twig', [
            'categories' => $categories,
            'maximum_appointments' => $maximumAppointments,
        ]);
    }

    #[Route('/new', name: 'app_categoryR_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRRepository $repository): Response
    {
        $category = new CategoryR();
        $form = $this->createForm(CategoryRType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($category, true);
            $this->addFlash('success', 'La catégorie de rendez-vous a été ajoutée.');

            return $this->redirectToRoute('app_categoryR_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_r/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categoryR_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity] CategoryR $category, CategoryRRepository $repository): Response
    {
        $form = $this->createForm(CategoryRType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($category, true);
            $this->addFlash('success', 'La catégorie de rendez-vous a été modifiée.');

            return $this->redirectToRoute('app_categoryR_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_r/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_categoryR_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity] CategoryR $category, CategoryRRepository $repository): Response
    {
        if (!$this->isCsrfTokenValid('delete-appointment-category-'.$category->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if (!$category->getRDVs()->isEmpty()) {
            $this->addFlash('warning', 'Cette catégorie contient des rendez-vous et ne peut pas être supprimée.');

            return $this->redirectToRoute('app_categoryR_index');
        }

        $repository->remove($category, true);
        $this->addFlash('success', 'La catégorie de rendez-vous a été supprimée.');

        return $this->redirectToRoute('app_categoryR_index', [], Response::HTTP_SEE_OTHER);
    }
}
