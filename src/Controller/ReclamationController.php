<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

final class ReclamationController extends AbstractController
{
    #[Route('/recherchereclamation', name: 'app_reclamation_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        return $this->redirectToRoute('app_reclamation', [
            'q' => trim((string) $request->query->get('q')),
        ]);
    }

    #[Route('/pdf', name: 'pdf', methods: ['GET'])]
    public function pdf(Request $request, ReclamationRepository $repository): Response
    {
        $reclamations = $repository->createListQueryBuilder((string) $request->query->get('q', ''))
            ->getQuery()
            ->getResult();

        $options = new Options();
        $options->setDefaultFont('Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->renderView('reclamation/pdf.html.twig', [
            'reclamations' => $reclamations,
        ]));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $disposition = (new ResponseHeaderBag())->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'reclamations.pdf'
        );

        return new Response($dompdf->output(), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition,
        ]);
    }

    #[Route('/reclamation', name: 'app_reclamation', methods: ['GET'])]
    public function index(Request $request, ReclamationRepository $repository, PaginatorInterface $paginator): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $pagination = $paginator->paginate(
            $repository->createListQueryBuilder($search),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('tables-data.html.twig', [
            'complaints' => $pagination,
            'searchTerm' => $search,
        ]);
    }

    #[Route('/addReclamation', name: 'addReclamation', methods: ['GET', 'POST'])]
    public function addReclamation(Request $request, ReclamationRepository $repository): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($reclamation, true);
            $this->addFlash('success', 'Votre réclamation a été enregistrée.');

            return $this->redirectToRoute('addReclamation');
        }

        return $this->render('ajouterRec.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    #[Route('/suppReclamation/{id}', name: 'suprimerReclamation', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity] Reclamation $reclamation,
        ReclamationRepository $repository,
        ReponseRepository $reponseRepository
    ): Response {
        if (!$this->isCsrfTokenValid('delete-complaint-'.$reclamation->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($reponseRepository->findOneBy(['relationReclamation' => $reclamation]) !== null) {
            $this->addFlash('warning', "Supprimez d'abord la réponse associée à cette réclamation.");

            return $this->redirectToRoute('app_reclamation');
        }

        $repository->remove($reclamation, true);
        $this->addFlash('success', 'La réclamation a été supprimée.');

        return $this->redirectToRoute('app_reclamation');
    }

    #[Route('/updateReclamation/{id}', name: 'updateReclamation', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity] Reclamation $reclamation, ReclamationRepository $repository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($reclamation, true);
            $this->addFlash('success', 'La réclamation a été modifiée.');

            return $this->redirectToRoute('app_reclamation');
        }

        return $this->render('ajouterRec.html.twig', [
            'f' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }
}
