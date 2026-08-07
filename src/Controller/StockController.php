<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Stock;
use App\Form\ChoixType;
use App\Form\EquipementType;
use App\Form\MedType;
use App\Form\StockType;
use App\Repository\CategorieRepository;
use App\Repository\StockRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StockController extends AbstractController
{
    #[Route('/stock', name: 'display_stock', methods: ['GET'])]
    public function index(
        Request $request,
        StockRepository $repository,
        CategorieRepository $categoryRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $kind = (string) $request->query->get('kind', '');
        $categoryId = $request->query->getInt('category') ?: null;

        return $this->render('stock/index.html.twig', [
            'stocks' => $paginator->paginate(
                $repository->createListQueryBuilder($search, $kind, $categoryId),
                $request->query->getInt('page', 1),
                12
            ),
            'categories' => $categoryRepository->findBy([], ['libelle' => 'ASC']),
            'searchTerm' => $search,
            'selectedKind' => $kind,
            'selectedCategory' => $categoryId,
        ]);
    }

    #[Route('/admin', name: 'display_admin', methods: ['GET'])]
    public function indexAdmin(): Response
    {
        return $this->redirectToRoute('app_index');
    }

    #[Route('/addStock', name: 'add_stock', methods: ['GET', 'POST'])]
    public function addStock(Request $request, StockRepository $repository): Response
    {
        return $this->handleStockForm($request, new Stock(), StockType::class, $repository, 'stock/ajoutStock.html.twig');
    }

    #[Route('/choixCategorie', name: 'choix', methods: ['GET', 'POST'])]
    public function choix(Request $request): Response
    {
        $stock = new Stock();
        $form = $this->createForm(ChoixType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $route = $form->get('kind')->getData() === 'medicine' ? 'add_med' : 'add_equipement';

            return $this->redirectToRoute($route, ['category' => $stock->getCategorie()?->getId()]);
        }

        return $this->render('stock/choixCateStock.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    #[Route('/addMed', name: 'add_med', methods: ['GET', 'POST'])]
    public function addMed(
        Request $request,
        StockRepository $repository,
        CategorieRepository $categorieRepository
    ): Response {
        $stock = new Stock();
        $this->preselectCategory($request, $stock, $categorieRepository);

        return $this->handleStockForm($request, $stock, MedType::class, $repository, 'stock/ajoutMed.html.twig');
    }

    #[Route('/addEquipement', name: 'add_equipement', methods: ['GET', 'POST'])]
    public function addEquipement(
        Request $request,
        StockRepository $repository,
        CategorieRepository $categorieRepository
    ): Response {
        $stock = new Stock();
        $this->preselectCategory($request, $stock, $categorieRepository);

        return $this->handleStockForm($request, $stock, EquipementType::class, $repository, 'stock/ajoutEquipement.html.twig');
    }

    #[Route('/removeStock/{id}', name: 'supp_stock', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity] Stock $stock, StockRepository $repository): Response
    {
        if (!$this->isCsrfTokenValid('delete-stock-'.$stock->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $repository->remove($stock, true);
        $this->addFlash('success', 'Le produit a été supprimé du stock.');

        return $this->redirectToRoute('display_stock');
    }

    #[Route('/modifStock/{id}', name: 'modif_stock', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity] Stock $stock, StockRepository $repository): Response
    {
        return $this->handleStockForm($request, $stock, StockType::class, $repository, 'stock/modifStock.html.twig');
    }

    #[Route('/Error', name: 'error', methods: ['GET'])]
    public function erreur(): Response
    {
        return $this->render('Error/erreur.html.twig');
    }

    /**
     * @param class-string $formType
     */
    private function handleStockForm(
        Request $request,
        Stock $stock,
        string $formType,
        StockRepository $repository,
        string $template
    ): Response {
        $form = $this->createForm($formType, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($stock, true);
            $this->addFlash('success', 'Le stock a été enregistré.');

            return $this->redirectToRoute('display_stock');
        }

        return $this->render($template, [
            'f' => $form->createView(),
            'stock' => $stock,
        ]);
    }

    private function preselectCategory(Request $request, Stock $stock, CategorieRepository $repository): void
    {
        $categoryId = $request->query->getInt('category');
        if ($categoryId > 0 && ($category = $repository->find($categoryId)) !== null) {
            $stock->setCategorie($category);
        }
    }
}
