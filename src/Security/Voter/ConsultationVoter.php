<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Consultation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ConsultationVoter extends Voter
{
    public const VIEW = 'CONSULTATION_VIEW';
    public const EDIT = 'CONSULTATION_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Consultation && in_array($attribute, [self::VIEW, self::EDIT], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User || !$subject instanceof Consultation) {
            return false;
        }

        $appointment = $subject->getAppointment();
        $isAssignedDoctor = in_array(User::ROLE_DOCTOR, $user->getRoles(), true)
            && $appointment?->getDoctor()?->getId() === $user->getId();
        $isPatientOwner = in_array(User::ROLE_PATIENT, $user->getRoles(), true)
            && $appointment?->getPatient()?->getId() === $user->getId();

        return match ($attribute) {
            self::VIEW => $isAssignedDoctor || $isPatientOwner,
            self::EDIT => $isAssignedDoctor,
            default => false,
        };
    }
}
