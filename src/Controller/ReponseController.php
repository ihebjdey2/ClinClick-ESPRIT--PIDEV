<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReponseController extends AbstractController
{
    #[Route('/reponse', name: 'app_reponse', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ReponseRepository $repository): Response
    {
        $pagination = $paginator->paginate(
            $repository->createQueryBuilder('response')->orderBy('response.id', 'DESC'),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('reponse/reponse.html.twig', [
            'responses' => $pagination,
        ]);
    }

    #[Route('/suppReponse/{id}', name: 'suprimerReponse', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity] Reponse $reponse, ReponseRepository $repository): Response
    {
        if (!$this->isCsrfTokenValid('delete-response-'.$reponse->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $repository->remove($reponse, true);
        $this->addFlash('success', 'La réponse a été supprimée sans supprimer la réclamation.');

        return $this->redirectToRoute('app_reponse');
    }

    #[Route('/addReponse/{id}', name: 'addReponse', methods: ['GET', 'POST'])]
    public function addReponse(Request $request, #[MapEntity] Reclamation $reclamation, ReponseRepository $repository): Response
    {
        if ($repository->findOneBy(['relationReclamation' => $reclamation]) instanceof Reponse) {
            $this->addFlash('warning', 'Une réponse existe déjà pour cette réclamation.');

            return $this->redirectToRoute('app_reponse');
        }

        $reponse = (new Reponse())->setRelationReclamation($reclamation);
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setEtat(true);
            $repository->save($reponse, true);
            $this->addFlash('success', 'La réponse a été enregistrée.');

            return $this->redirectToRoute('app_reponse');
        }

        return $this->render('reponse/index.html.twig', [
            'f' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }
}
