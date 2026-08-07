<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\RDV;
use App\Entity\User;
use App\Enum\AppointmentStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class AppointmentVoter extends Voter
{
    public const VIEW = 'APPOINTMENT_VIEW';
    public const EDIT = 'APPOINTMENT_EDIT';
    public const CANCEL = 'APPOINTMENT_CANCEL';
    public const CHANGE_STATUS = 'APPOINTMENT_CHANGE_STATUS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof RDV && in_array($attribute, [self::VIEW, self::EDIT, self::CANCEL, self::CHANGE_STATUS], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User || !$subject instanceof RDV) {
            return false;
        }

        $roles = $user->getRoles();
        if (in_array(User::ROLE_ADMIN, $roles, true) || in_array(User::ROLE_RECEPTIONIST, $roles, true)) {
            return match ($attribute) {
                self::VIEW => true,
                self::EDIT => $subject->getStatus() === AppointmentStatus::PENDING,
                self::CANCEL => in_array($subject->getStatus(), [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true),
                self::CHANGE_STATUS => in_array($subject->getStatus(), [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true),
                default => false,
            };
        }

        $isPatientOwner = in_array(User::ROLE_PATIENT, $roles, true) && $subject->getPatient()?->getId() === $user->getId();
        $isAssignedDoctor = in_array(User::ROLE_DOCTOR, $roles, true) && $subject->getDoctor()?->getId() === $user->getId();

        return match ($attribute) {
            self::VIEW => $isPatientOwner || $isAssignedDoctor,
            self::EDIT => $isPatientOwner && $subject->getStatus() === AppointmentStatus::PENDING,
            self::CANCEL => $isPatientOwner && in_array($subject->getStatus(), [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true),
            self::CHANGE_STATUS => $isAssignedDoctor,
            default => false,
        };
    }
}
