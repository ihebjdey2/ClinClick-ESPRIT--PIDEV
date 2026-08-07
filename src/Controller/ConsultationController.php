<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Prescription;
use App\Entity\RDV;
use App\Entity\User;
use App\Exception\MedicalRecordRuleException;
use App\Form\ConsultationType;
use App\Form\PrescriptionType;
use App\Repository\ConsultationRepository;
use App\Security\Voter\ConsultationVoter;
use App\Service\ConsultationService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/medical/consultations')]
final class ConsultationController extends AbstractController
{
    #[Route('/', name: 'app_consultation_index', methods: ['GET'])]
    public function index(Request $request, ConsultationRepository $repository, PaginatorInterface $paginator): Response
    {
        $consultations = $paginator->paginate(
            $repository->createScopedQueryBuilder($this->currentUser()),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('consultation/index.html.twig', ['consultations' => $consultations]);
    }

    #[Route('/new/{id}', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        #[MapEntity] RDV $appointment,
        ConsultationService $consultationService
    ): Response {
        $user = $this->currentUser();
        if (!in_array(User::ROLE_DOCTOR, $user->getRoles(), true)
            || $appointment->getDoctor()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $consultation = new Consultation();
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $consultationService->create($appointment, $consultation, $user);
                $this->addFlash('success', 'La consultation a été enregistrée et le rendez-vous marqué comme terminé.');

                return $this->redirectToRoute('app_consultation_show', ['id' => $consultation->getId()], Response::HTTP_SEE_OTHER);
            } catch (MedicalRecordRuleException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('consultation/new.html.twig', [
            'appointment' => $appointment,
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(#[MapEntity] Consultation $consultation): Response
    {
        $this->denyAccessUnlessGranted(ConsultationVoter::VIEW, $consultation);

        return $this->render('consultation/show.html.twig', ['consultation' => $consultation]);
    }

    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] Consultation $consultation,
        ConsultationService $consultationService
    ): Response {
        $this->denyAccessUnlessGranted(ConsultationVoter::EDIT, $consultation);
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $consultationService->update($consultation, $this->currentUser());
                $this->addFlash('success', 'Le dossier médical a été mis à jour.');

                return $this->redirectToRoute('app_consultation_show', ['id' => $consultation->getId()], Response::HTTP_SEE_OTHER);
            } catch (MedicalRecordRuleException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('consultation/edit.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/prescriptions/new', name: 'app_prescription_new', methods: ['GET', 'POST'])]
    public function newPrescription(
        Request $request,
        #[MapEntity] Consultation $consultation,
        ConsultationService $consultationService
    ): Response {
        $this->denyAccessUnlessGranted(ConsultationVoter::EDIT, $consultation);
        $prescription = new Prescription();
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $consultationService->addPrescription($consultation, $prescription, $this->currentUser());
                $this->addFlash('success', 'La prescription a été ajoutée.');

                return $this->redirectToRoute('app_consultation_show', ['id' => $consultation->getId()], Response::HTTP_SEE_OTHER);
            } catch (MedicalRecordRuleException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('consultation/prescription_new.html.twig', [
            'consultation' => $consultation,
            'prescription' => $prescription,
            'form' => $form,
        ]);
    }

    private function currentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }
}
