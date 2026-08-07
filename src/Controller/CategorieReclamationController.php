<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CategorieReclamation;
use App\Form\CategorieReclamationType;
use App\Repository\CategorieReclamationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieReclamationController extends AbstractController
{
    #[Route('/categorieReclamation', name: 'app_categorieReclamation', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        CategorieReclamationRepository $repository
    ): Response {
        $categories = $paginator->paginate(
            $repository->createQueryBuilder('category')->orderBy('category.nom', 'ASC'),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('tablesCategorieReclamation.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/addCategorieReclamation', name: 'addCategorieReclamation', methods: ['GET', 'POST'])]
    public function add(Request $request, CategorieReclamationRepository $repository): Response
    {
        $category = new CategorieReclamation();
        $form = $this->createForm(CategorieReclamationType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($category, true);
            $this->addFlash('success', 'La catégorie de réclamation a été ajoutée.');

            return $this->redirectToRoute('app_categorieReclamation');
        }

        return $this->render('ajouterCat.html.twig', ['f' => $form->createView()]);
    }

    #[Route('/suppCategorieReclamation/{id}', name: 'suprimerCategorieReclamation', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity] CategorieReclamation $category,
        CategorieReclamationRepository $repository
    ): Response {
        if (!$this->isCsrfTokenValid('delete-complaint-category-'.$category->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if (!$category->getReclamations()->isEmpty()) {
            $this->addFlash('warning', 'Cette catégorie est utilisée et ne peut pas être supprimée.');

            return $this->redirectToRoute('app_categorieReclamation');
        }

        $repository->remove($category, true);
        $this->addFlash('success', 'La catégorie de réclamation a été supprimée.');

        return $this->redirectToRoute('app_categorieReclamation');
    }

    #[Route('/updateCategorieReclamation/{id}', name: 'updateCategorieReclamation', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] CategorieReclamation $category,
        CategorieReclamationRepository $repository
    ): Response {
        $form = $this->createForm(CategorieReclamationType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($category, true);
            $this->addFlash('success', 'La catégorie de réclamation a été modifiée.');

            return $this->redirectToRoute('app_categorieReclamation');
        }

        return $this->render('ajouterCat.html.twig', ['f' => $form]);
    }
}
