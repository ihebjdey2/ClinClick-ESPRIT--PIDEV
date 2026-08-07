<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DoctorAvailability;
use App\Entity\User;
use App\Exception\AvailabilityRuleException;
use App\Form\DoctorAvailabilityType;
use App\Repository\DoctorAvailabilityRepository;
use App\Service\DoctorAvailabilityService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/doctor/availability')]
final class DoctorAvailabilityController extends AbstractController
{
    #[Route('', name: 'app_doctor_availability_index', methods: ['GET'])]
    public function index(DoctorAvailabilityRepository $repository): Response
    {
        $user = $this->currentUser();
        $criteria = in_array(User::ROLE_ADMIN, $user->getRoles(), true) ? [] : ['doctor' => $user];

        return $this->render('doctor_availability/index.html.twig', [
            'availabilities' => $repository->findBy($criteria, ['doctor' => 'ASC', 'dayOfWeek' => 'ASC', 'startTime' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_doctor_availability_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DoctorAvailabilityService $service): Response
    {
        $user = $this->currentUser();
        $showDoctor = in_array(User::ROLE_ADMIN, $user->getRoles(), true);
        $availability = new DoctorAvailability();
        if (!$showDoctor) {
            $availability->setDoctor($user);
        }

        return $this->handleForm($request, $availability, $service, $showDoctor, 'Ajouter une disponibilité');
    }

    #[Route('/{id}/edit', name: 'app_doctor_availability_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity] DoctorAvailability $availability, DoctorAvailabilityService $service): Response
    {
        $user = $this->currentUser();
        $showDoctor = in_array(User::ROLE_ADMIN, $user->getRoles(), true);
        if (!$showDoctor && $availability->getDoctor()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        return $this->handleForm($request, $availability, $service, $showDoctor, 'Modifier une disponibilité');
    }

    #[Route('/{id}/delete', name: 'app_doctor_availability_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity] DoctorAvailability $availability, DoctorAvailabilityService $service): Response
    {
        $user = $this->currentUser();
        if (!in_array(User::ROLE_ADMIN, $user->getRoles(), true) && $availability->getDoctor()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isCsrfTokenValid('delete-availability-'.$availability->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        try {
            $service->remove($availability);
            $this->addFlash('success', 'La disponibilité a été supprimée.');
        } catch (AvailabilityRuleException $exception) {
            $this->addFlash('warning', $exception->getMessage());
        }

        return $this->redirectToRoute('app_doctor_availability_index');
    }

    private function handleForm(
        Request $request,
        DoctorAvailability $availability,
        DoctorAvailabilityService $service,
        bool $showDoctor,
        string $pageTitle
    ): Response {
        $form = $this->createForm(DoctorAvailabilityType::class, $availability, ['show_doctor' => $showDoctor]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($availability);
                $this->addFlash('success', 'La disponibilité a été enregistrée.');

                return $this->redirectToRoute('app_doctor_availability_index');
            } catch (AvailabilityRuleException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('doctor_availability/form.html.twig', [
            'availabilityForm' => $form,
            'pageTitle' => $pageTitle,
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
