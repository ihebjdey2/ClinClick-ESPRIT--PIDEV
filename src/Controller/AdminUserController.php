<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\ParticiperRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
final class AdminUserController extends AbstractController
{
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $repository, PaginatorInterface $paginator): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $role = (string) $request->query->get('role', '');
        $role = in_array($role, User::BUSINESS_ROLES, true) ? $role : null;
        $users = $paginator->paginate(
            $repository->createAdminListQueryBuilder($search, $role),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'searchTerm' => $search,
            'selectedRole' => $role,
            'roleChoices' => $this->roleLabels(),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserRepository $repository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = (new User())->setRoles([User::ROLE_PATIENT])->setIsVerified(true);
        $form = $this->createForm(AdminUserType::class, $user, ['password_required' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, (string) $form->get('plainPassword')->getData()));
            $repository->save($user, true);
            $this->addFlash('success', 'Le compte a été créé.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/form.html.twig', [
            'userForm' => $form,
            'managedUser' => $user,
            'pageTitle' => 'Créer un compte',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] User $user,
        UserRepository $repository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $originalRoles = $user->getRoles();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user === $this->getUser() && !in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
                $user->setRoles($originalRoles);
                $form->get('roles')->addError(new FormError("Vous ne pouvez pas retirer votre propre rôle administrateur."));
            } else {
                $plainPassword = (string) $form->get('plainPassword')->getData();
                if ($plainPassword !== '') {
                    $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                }
                $repository->save($user, true);
                $this->addFlash('success', 'Le compte a été modifié.');

                return $this->redirectToRoute('app_admin_user_index');
            }
        }

        return $this->render('admin/user/form.html.twig', [
            'userForm' => $form,
            'managedUser' => $user,
            'pageTitle' => 'Modifier un compte',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity] User $user,
        UserRepository $repository,
        ParticiperRepository $participationRepository
    ): Response {
        if (!$this->isCsrfTokenValid('delete-user-'.$user->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if ($user === $this->getUser()) {
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer votre propre compte.');

            return $this->redirectToRoute('app_admin_user_index');
        }
        if ($participationRepository->count(['user' => $user]) > 0) {
            $this->addFlash('warning', "Ce compte possède un historique de participation et ne peut pas être supprimé.");

            return $this->redirectToRoute('app_admin_user_index');
        }

        $repository->remove($user, true);
        $this->addFlash('success', 'Le compte a été supprimé.');

        return $this->redirectToRoute('app_admin_user_index');
    }

    /** @return array<string, string> */
    private function roleLabels(): array
    {
        return [
            User::ROLE_ADMIN => 'Administrateur',
            User::ROLE_DOCTOR => 'Médecin',
            User::ROLE_RECEPTIONIST => 'Réceptionniste',
            User::ROLE_PATIENT => 'Patient',
        ];
    }
}
