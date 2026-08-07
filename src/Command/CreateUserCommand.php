<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Crée un compte local avec un rôle métier sans exposer le mot de passe dans la ligne de commande.'
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Adresse e-mail fictive ou locale')
            ->addArgument('role', InputArgument::REQUIRED, 'ROLE_ADMIN, ROLE_DOCTOR, ROLE_RECEPTIONIST ou ROLE_PATIENT')
            ->addOption('first-name', null, InputOption::VALUE_REQUIRED, 'Prénom')
            ->addOption('last-name', null, InputOption::VALUE_REQUIRED, 'Nom')
            ->addOption('birth-date', null, InputOption::VALUE_REQUIRED, 'Date de naissance au format YYYY-MM-DD')
            ->addOption('gender', null, InputOption::VALUE_REQUIRED, 'femme, homme ou non_specifie');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = strtolower(trim((string) $input->getArgument('email')));
        $role = strtoupper(trim((string) $input->getArgument('role')));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $io->error("L'adresse e-mail n'est pas valide.");

            return Command::INVALID;
        }
        if (!in_array($role, User::BUSINESS_ROLES, true)) {
            $io->error('Le rôle métier est invalide.');

            return Command::INVALID;
        }
        if ($this->repository->findOneBy(['email' => $email]) instanceof User) {
            $io->error('Un compte existe déjà avec cette adresse e-mail.');

            return Command::FAILURE;
        }

        $firstName = trim((string) ($input->getOption('first-name') ?: $io->ask('Prénom', 'Demo')));
        $lastName = trim((string) ($input->getOption('last-name') ?: $io->ask('Nom', 'CliniClic')));
        $birthDateValue = (string) ($input->getOption('birth-date') ?: $io->ask('Date de naissance (YYYY-MM-DD)', '1990-01-01'));
        $gender = (string) ($input->getOption('gender') ?: $io->choice('Genre', ['femme', 'homme', 'non_specifie'], 'non_specifie'));
        $birthDate = DateTime::createFromFormat('!Y-m-d', $birthDateValue) ?: null;

        if (!$birthDate instanceof DateTime || $birthDate->format('Y-m-d') !== $birthDateValue) {
            $io->error('La date de naissance doit respecter le format YYYY-MM-DD.');

            return Command::INVALID;
        }

        $password = (string) $io->askHidden('Mot de passe (12 caractères minimum)');
        if (mb_strlen($password) < 12) {
            $io->error('Le mot de passe doit contenir au moins 12 caractères.');

            return Command::INVALID;
        }
        if ($password !== (string) $io->askHidden('Confirmez le mot de passe')) {
            $io->error('Les mots de passe ne correspondent pas.');

            return Command::INVALID;
        }

        $user = (new User())
            ->setEmail($email)
            ->setPrenom($firstName)
            ->setNom($lastName)
            ->setDateNaissance($birthDate)
            ->setGenre($gender)
            ->setRoles([$role])
            ->setIsVerified(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $violations = $this->validator->validate($user);
        if ($violations->count() > 0) {
            foreach ($violations as $violation) {
                $io->error($violation->getPropertyPath().' : '.$violation->getMessage());
            }

            return Command::INVALID;
        }

        $this->repository->save($user, true);
        $io->success(sprintf('Compte %s créé avec le rôle %s.', $email, $role));

        return Command::SUCCESS;
    }
}
