<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'display_categorie', methods: ['GET'])]
    public function index(CategorieRepository $repository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $repository->findBy([], ['libelle' => 'ASC']),
        ]);
    }

    #[Route('/addCategorie', name: 'add_categorie', methods: ['GET', 'POST'])]
    public function addCategorie(Request $request, CategorieRepository $repository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($categorie, true);
            $this->addFlash('success', 'La catégorie a été ajoutée.');

            return $this->redirectToRoute('display_categorie');
        }

        return $this->render('categorie/ajoutCategorie.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    #[Route('/removeCategorie/{id}', name: 'supp_categorie', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity] Categorie $categorie, CategorieRepository $repository): Response
    {
        if (!$this->isCsrfTokenValid('delete-category-'.$categorie->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if (!$categorie->getStocks()->isEmpty()) {
            $this->addFlash('warning', 'Cette catégorie est utilisée par du stock et ne peut pas être supprimée.');

            return $this->redirectToRoute('display_categorie');
        }

        $repository->remove($categorie, true);
        $this->addFlash('success', 'La catégorie a été supprimée.');

        return $this->redirectToRoute('display_categorie');
    }

    #[Route('/modifCategorie/{id}', name: 'modif_categorie', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity] Categorie $categorie, CategorieRepository $repository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($categorie, true);
            $this->addFlash('success', 'La catégorie a été modifiée.');

            return $this->redirectToRoute('display_categorie');
        }

        return $this->render('categorie/ajoutCategorie.html.twig', [
            'f' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }
}
