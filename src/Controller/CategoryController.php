<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);
            $this->addFlash('success', 'La catégorie d’événement a été créée.');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(#[MapEntity] Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity] Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);
            $this->addFlash('success', 'La catégorie d’événement a été mise à jour.');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity] Category $category, CategoryRepository $categoryRepository): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$category->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if (!$category->getEvenements()->isEmpty()) {
            $this->addFlash('warning', 'Cette catégorie contient des événements et ne peut pas être supprimée.');

            return $this->redirectToRoute('app_category_index');
        }

        $categoryRepository->remove($category, true);
        $this->addFlash('success', 'La catégorie a été supprimée.');

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
