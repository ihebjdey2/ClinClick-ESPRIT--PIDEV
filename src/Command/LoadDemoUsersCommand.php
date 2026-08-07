<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\CategoryR;
use App\Entity\DoctorAvailability;
use App\Entity\User;
use App\Repository\CategoryRRepository;
use App\Repository\DoctorAvailabilityRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:demo:users',
    description: 'Crée quatre comptes entièrement fictifs pour la démonstration locale.'
)]
final class LoadDemoUsersCommand extends Command
{
    private const DEMO_PASSWORD = 'ClinicDemo!2026';

    private const ACCOUNTS = [
        ['admin@cliniclic.test', 'Admin', 'CliniClic', User::ROLE_ADMIN, 'non_specifie'],
        ['doctor@cliniclic.test', 'Docteur', 'Martin', User::ROLE_DOCTOR, 'non_specifie'],
        ['reception@cliniclic.test', 'Accueil', 'CliniClic', User::ROLE_RECEPTIONIST, 'non_specifie'],
        ['patient@cliniclic.test', 'Camille', 'Bernard', User::ROLE_PATIENT, 'non_specifie'],
    ];

    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly KernelInterface $kernel,
        private readonly DoctorAvailabilityRepository $availabilityRepository,
        private readonly CategoryRRepository $categoryRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($this->kernel->getEnvironment() === 'prod') {
            $io->error('Cette commande de démonstration est interdite en production.');

            return Command::FAILURE;
        }

        $created = 0;
        foreach (self::ACCOUNTS as [$email, $firstName, $lastName, $role, $gender]) {
            if ($this->repository->findOneBy(['email' => $email]) instanceof User) {
                continue;
            }

            $user = (new User())
                ->setEmail($email)
                ->setPrenom($firstName)
                ->setNom($lastName)
                ->setDateNaissance(new DateTime('1990-01-01'))
                ->setGenre($gender)
                ->setRoles([$role])
                ->setIsVerified(true);
            $user->setPassword($this->passwordHasher->hashPassword($user, self::DEMO_PASSWORD));
            $this->repository->save($user, true);
            ++$created;
        }

        foreach (['Consultation générale', 'Consultation de suivi', 'Contrôle'] as $name) {
            if (!$this->categoryRepository->findOneBy(['nom' => $name]) instanceof CategoryR) {
                $this->categoryRepository->save((new CategoryR())->setNom($name), true);
            }
        }

        $doctor = $this->repository->findOneBy(['email' => 'doctor@cliniclic.test']);
        if ($doctor instanceof User && $this->availabilityRepository->count(['doctor' => $doctor]) === 0) {
            for ($day = 1; $day <= 5; ++$day) {
                $availability = (new DoctorAvailability())
                    ->setDoctor($doctor)
                    ->setDayOfWeek($day)
                    ->setStartTime(new DateTimeImmutable('08:00'))
                    ->setEndTime(new DateTimeImmutable('17:00'));
                $this->availabilityRepository->save($availability, true);
            }
        }

        $io->success(sprintf('%d compte(s) fictif(s) disponible(s). Mot de passe local : %s', $created, self::DEMO_PASSWORD));

        return Command::SUCCESS;
    }
}
