<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\RDV;
use App\Entity\User;
use App\Enum\AppointmentStatus;
use App\Exception\AppointmentRuleException;
use App\Form\RDVType;
use App\Repository\RDVRepository;
use App\Security\Voter\AppointmentVoter;
use App\Service\AppointmentService;
use DateTimeImmutable;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rendez-vous')]
final class RDVController extends AbstractController
{
    #[Route('/', name: 'app_RDV_index', methods: ['GET'])]
    public function index(Request $request, RDVRepository $repository, PaginatorInterface $paginator): Response
    {
        $user = $this->currentUser();
        $search = trim((string) $request->query->get('q', ''));
        $statusValue = (string) $request->query->get('status', '');
        $status = AppointmentStatus::tryFrom($statusValue);
        $dateValue = (string) $request->query->get('date', '');
        $date = null;
        if ($dateValue !== '') {
            try {
                $date = new DateTimeImmutable($dateValue);
            } catch (Exception) {
                $dateValue = '';
            }
        }

        $appointments = $paginator->paginate(
            $repository->createScopedListQueryBuilder($user, $search, $status, $date),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('rdv/index.html.twig', [
            'appointments' => $appointments,
            'searchTerm' => $search,
            'selectedStatus' => $status,
            'selectedDate' => $dateValue,
            'statuses' => AppointmentStatus::cases(),
        ]);
    }

    #[Route('/new', name: 'app_RDV_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AppointmentService $appointmentService): Response
    {
        $user = $this->currentUser();
        $showPatient = $this->canSelectPatient($user);

        if (in_array(User::ROLE_DOCTOR, $user->getRoles(), true) && !$showPatient) {
            throw $this->createAccessDeniedException('Un médecin ne peut pas créer un rendez-vous depuis cet écran.');
        }

        $appointment = new RDV();
        if (!$showPatient) {
            $appointment->setPatient($user);
        }

        $form = $this->createForm(RDVType::class, $appointment, ['show_patient' => $showPatient]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $appointmentService->save($appointment);
                $this->addFlash('success', 'Le rendez-vous a été demandé.');

                return $this->redirectToRoute('app_RDV_index', [], Response::HTTP_SEE_OTHER);
            } catch (AppointmentRuleException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('rdv/new.html.twig', [
            'RDV' => $appointment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_RDV_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity] RDV $appointment, AppointmentService $appointmentService): Response
    {
        $this->denyAccessUnlessGranted(AppointmentVoter::EDIT, $appointment);
        $showPatient = $this->canSelectPatient($this->currentUser());
        $form = $this->createForm(RDVType::class, $appointment, ['show_patient' => $showPatient]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $appointmentService->save($appointment);
                $this->addFlash('success', 'Le rendez-vous a été modifié.');

                return $this->redirectToRoute('app_RDV_index', [], Response::HTTP_SEE_OTHER);
            } catch (AppointmentRuleException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('rdv/edit.html.twig', [
            'RDV' => $appointment,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_RDV_delete', methods: ['POST'])]
    public function cancel(Request $request, #[MapEntity] RDV $appointment, AppointmentService $appointmentService): Response
    {
        $this->denyAccessUnlessGranted(AppointmentVoter::CANCEL, $appointment);
        if (!$this->isCsrfTokenValid('cancel-appointment-'.$appointment->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        try {
            $appointmentService->changeStatus($appointment, AppointmentStatus::CANCELLED, $this->currentUser());
            $this->addFlash('success', 'Le rendez-vous a été annulé et son créneau est de nouveau disponible.');
        } catch (AppointmentRuleException $exception) {
            $this->addFlash('warning', $exception->getMessage());
        }

        return $this->redirectToRoute('app_RDV_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/status/{status}', name: 'app_RDV_status', methods: ['POST'])]
    public function status(
        Request $request,
        #[MapEntity] RDV $appointment,
        string $status,
        AppointmentService $appointmentService
    ): Response {
        $this->denyAccessUnlessGranted(AppointmentVoter::CHANGE_STATUS, $appointment);
        if (!$this->isCsrfTokenValid('status-appointment-'.$appointment->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $newStatus = AppointmentStatus::tryFrom($status);
        if ($newStatus === null) {
            throw $this->createNotFoundException('Statut inconnu.');
        }

        try {
            $appointmentService->changeStatus($appointment, $newStatus, $this->currentUser());
            $this->addFlash('success', 'Le statut du rendez-vous a été mis à jour.');
        } catch (AppointmentRuleException $exception) {
            $this->addFlash('warning', $exception->getMessage());
        }

        return $this->redirectToRoute('app_RDV_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/r/search_recc', name: 'search_RDV', methods: ['GET'])]
    public function search(Request $request, RDVRepository $repository): JsonResponse
    {
        $appointments = $repository->createScopedListQueryBuilder(
            $this->currentUser(),
            (string) $request->query->get('searchValue', '')
        )->setMaxResults(25)->getQuery()->getResult();

        return $this->json(array_map(static fn (RDV $appointment): array => [
            'id' => $appointment->getId(),
            'nom' => $appointment->getNom(),
            'patient' => $appointment->getPatient()?->getFullName(),
            'doctor' => $appointment->getDoctor()?->getFullName(),
            'scheduledAt' => $appointment->getScheduledAt()?->format(DATE_ATOM),
            'status' => $appointment->getStatus()->value,
            'category' => $appointment->getCategory()?->getNom(),
        ], $appointments));
    }

    private function currentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

    private function canSelectPatient(User $user): bool
    {
        return in_array(User::ROLE_ADMIN, $user->getRoles(), true)
            || in_array(User::ROLE_RECEPTIONIST, $user->getRoles(), true);
    }
}
