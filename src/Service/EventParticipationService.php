<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Evenement;
use App\Entity\Participer;
use App\Entity\User;
use App\Exception\EventParticipationException;
use App\Repository\ParticiperRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

final class EventParticipationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ParticiperRepository $participationRepository
    ) {
    }

    public function participate(Evenement $event, User $user, int $capacity): void
    {
        try {
            $this->entityManager->wrapInTransaction(function () use ($event, $user, $capacity): void {
                $this->entityManager->lock($event, LockMode::PESSIMISTIC_WRITE);

                if ($this->participationRepository->findOneBy(['user' => $user, 'event' => $event]) instanceof Participer) {
                    throw new EventParticipationException('Vous participez déjà à cet événement.');
                }
                if ($this->participationRepository->countForEvent($event) >= $capacity) {
                    throw new EventParticipationException('L’événement est complet.');
                }

                $this->entityManager->persist((new Participer())->setUser($user)->setEvent($event));
            });
        } catch (UniqueConstraintViolationException) {
            throw new EventParticipationException('Vous participez déjà à cet événement.');
        }
    }
}
