<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Enum\AppointmentStatus;
use App\Repository\DoctorAvailabilityRepository;
use App\Repository\RDVRepository;
use App\Repository\ReclamationRepository;
use App\Repository\StockRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;

final class DashboardService
{
    public function __construct(
        private readonly RDVRepository $appointmentRepository,
        private readonly UserRepository $userRepository,
        private readonly ReclamationRepository $complaintRepository,
        private readonly StockRepository $stockRepository,
        private readonly DoctorAvailabilityRepository $availabilityRepository
    ) {
    }

    /** @return array<string, mixed> */
    public function getFor(User $user): array
    {
        $today = new DateTimeImmutable('today');
        $roles = $user->getRoles();
        $data = [
            'role' => 'patient',
            'title' => 'Mon espace patient',
            'cards' => [],
            'upcoming' => $this->appointmentRepository->findUpcomingScoped($user),
            'show_patient_identity' => false,
        ];

        if (in_array(User::ROLE_ADMIN, $roles, true)) {
            $data['role'] = 'admin';
            $data['title'] = "Vue d'ensemble administrative";
            $data['cards'] = [
                ['label' => 'Patients', 'value' => $this->userRepository->countByBusinessRole(User::ROLE_PATIENT), 'icon' => 'bi-people'],
                ['label' => 'Médecins', 'value' => $this->userRepository->countByBusinessRole(User::ROLE_DOCTOR), 'icon' => 'bi-heart-pulse'],
                ['label' => "Rendez-vous aujourd'hui", 'value' => $this->appointmentRepository->countScoped($user, null, $today), 'icon' => 'bi-calendar-check'],
                ['label' => 'Réclamations en attente', 'value' => $this->complaintRepository->count(['etat' => false]), 'icon' => 'bi-chat-left-text'],
                ['label' => 'Alertes de stock', 'value' => $this->stockRepository->countLowStock(), 'icon' => 'bi-exclamation-triangle'],
            ];
            // Privacy by design: the administrator sees operational counts, not patient details.
            $data['upcoming'] = [];
        } elseif (in_array(User::ROLE_RECEPTIONIST, $roles, true)) {
            $data['role'] = 'receptionist';
            $data['title'] = "Accueil et rendez-vous";
            $data['cards'] = [
                ['label' => "Rendez-vous aujourd'hui", 'value' => $this->appointmentRepository->countScoped($user, null, $today), 'icon' => 'bi-calendar-check'],
                ['label' => 'Demandes en attente', 'value' => $this->appointmentRepository->countScoped($user, AppointmentStatus::PENDING), 'icon' => 'bi-hourglass-split'],
                ['label' => 'Patients enregistrés', 'value' => $this->userRepository->countByBusinessRole(User::ROLE_PATIENT), 'icon' => 'bi-people'],
                ['label' => 'Réclamations en attente', 'value' => $this->complaintRepository->count(['etat' => false]), 'icon' => 'bi-chat-left-text'],
            ];
            $data['show_patient_identity'] = true;
        } elseif (in_array(User::ROLE_DOCTOR, $roles, true)) {
            $data['role'] = 'doctor';
            $data['title'] = 'Mon activité médicale';
            $data['cards'] = [
                ['label' => "Rendez-vous aujourd'hui", 'value' => $this->appointmentRepository->countScoped($user, null, $today), 'icon' => 'bi-calendar-check'],
                ['label' => 'À confirmer', 'value' => $this->appointmentRepository->countScoped($user, AppointmentStatus::PENDING), 'icon' => 'bi-hourglass-split'],
                ['label' => 'Confirmés', 'value' => $this->appointmentRepository->countScoped($user, AppointmentStatus::CONFIRMED), 'icon' => 'bi-check2-circle'],
                ['label' => 'Plages disponibles', 'value' => $this->availabilityRepository->count(['doctor' => $user, 'isActive' => true]), 'icon' => 'bi-clock'],
            ];
            $data['show_patient_identity'] = true;
        } else {
            $data['cards'] = [
                ['label' => 'En attente', 'value' => $this->appointmentRepository->countScoped($user, AppointmentStatus::PENDING), 'icon' => 'bi-hourglass-split'],
                ['label' => 'Confirmés', 'value' => $this->appointmentRepository->countScoped($user, AppointmentStatus::CONFIRMED), 'icon' => 'bi-calendar-check'],
                ['label' => 'Terminés', 'value' => $this->appointmentRepository->countScoped($user, AppointmentStatus::COMPLETED), 'icon' => 'bi-check2-circle'],
                ['label' => 'Annulés', 'value' => $this->appointmentRepository->countScoped($user, AppointmentStatus::CANCELLED), 'icon' => 'bi-calendar-x'],
            ];
        }

        return $data;
    }
}
