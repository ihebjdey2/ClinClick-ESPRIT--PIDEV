<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\User;
use App\Exception\EventParticipationException;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\ParticiperRepository;
use App\Repository\UserRepository;
use App\Service\EventImageUploader;
use App\Service\EventParticipationService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class EvenementController extends AbstractController
{
    private const MAX_PARTICIPANTS = 5;

    #[Route('/evenement', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $eventRepository, ParticiperRepository $participationRepository): Response
    {
        $events = $eventRepository->findUpcoming();
        $eventIds = array_map(static fn (Evenement $event): int => (int) $event->getId(), $events);

        return $this->render('evenement/afficheEvent.html.twig', [
            'evenements' => $events,
            'participant_counts' => $participationRepository->countByEventIds($eventIds),
            'max_participants' => self::MAX_PARTICIPANTS,
        ]);
    }

    #[Route('/evenement/back', name: 'app_evenement_indexFront', methods: ['GET'])]
    public function indexFront(EvenementRepository $repository): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenements' => $repository->findBy([], ['date' => 'ASC']),
        ]);
    }

    #[Route('/evenement/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EvenementRepository $repository,
        EventImageUploader $imageUploader
    ): Response {
        $event = new Evenement();
        $form = $this->createForm(EvenementType::class, $event, ['image_required' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $event->getImageFile();
            if ($uploadedFile instanceof UploadedFile) {
                try {
                    $event->setImage($imageUploader->upload($uploadedFile));
                    $repository->save($event, true);
                    $this->addFlash('success', "L'événement a été créé.");

                    return $this->redirectToRoute('app_evenement_indexFront');
                } catch (FileException $exception) {
                    $form->get('imageFile')->addError(new FormError($exception->getMessage()));
                }
            }
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(#[MapEntity] Evenement $evenement): Response
    {
        return $this->render('detail.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/home', name: 'appHome', methods: ['GET'])]
    public function home(UserRepository $userRepository, EvenementRepository $eventRepository): Response
    {
        return $this->render('home1/home.html.twig', [
            'doctor_count' => $userRepository->countByBusinessRole(User::ROLE_DOCTOR),
            'upcoming_event_count' => $eventRepository->countUpcoming(),
            'upcoming_events' => $eventRepository->findUpcoming(3),
        ]);
    }

    #[Route('/evenement/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] Evenement $evenement,
        EvenementRepository $repository,
        EventImageUploader $imageUploader
    ): Response {
        $previousImage = $evenement->getImage();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $evenement->getImageFile();

            try {
                if ($uploadedFile instanceof UploadedFile) {
                    $evenement->setImage($imageUploader->upload($uploadedFile));
                }

                $repository->save($evenement, true);
                if ($uploadedFile instanceof UploadedFile) {
                    $imageUploader->remove($previousImage);
                }

                $this->addFlash('success', "L'événement a été modifié.");

                return $this->redirectToRoute('app_evenement_indexFront', [], Response::HTTP_SEE_OTHER);
            } catch (FileException $exception) {
                $form->get('imageFile')->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity] Evenement $evenement,
        EvenementRepository $repository,
        ParticiperRepository $participationRepository,
        EventImageUploader $imageUploader
    ): Response {
        if (!$this->isCsrfTokenValid('delete'.$evenement->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($participationRepository->countForEvent($evenement) > 0) {
            $this->addFlash('warning', "Cet événement possède des participants et ne peut pas être supprimé.");

            return $this->redirectToRoute('app_evenement_indexFront');
        }

        $image = $evenement->getImage();
        $repository->remove($evenement, true);
        $imageUploader->remove($image);
        $this->addFlash('success', "L'événement a été supprimé.");

        return $this->redirectToRoute('app_evenement_indexFront', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/evenement/{id}/participation', name: 'app_evenement_participer', methods: ['POST'])]
    public function participate(
        Request $request,
        #[MapEntity] Evenement $evenement,
        EventParticipationService $participationService,
        MailerInterface $mailer,
        string $mailerFrom
    ): Response {
        if (!$this->isCsrfTokenValid('participate-event-'.$evenement->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('warning', 'Vous devez être connecté pour participer à un événement.');

            return $this->redirectToRoute('app_evenement_index');
        }

        try {
            $participationService->participate($evenement, $user, self::MAX_PARTICIPANTS);
        } catch (EventParticipationException $exception) {
            $this->addFlash('warning', $exception->getMessage());
            return $this->redirectToRoute('app_evenement_index');
        }

        $this->addFlash('success', 'Votre participation a été enregistrée.');

        try {
            $mailer->send((new Email())
                ->from($mailerFrom)
                ->to((string) $user->getEmail())
                ->subject("Confirmation de participation")
                ->text("Votre participation à l'événement « ".$evenement->getTitre().' » est confirmée.'));
        } catch (TransportExceptionInterface) {
            $this->addFlash('warning', "La participation est enregistrée, mais l'e-mail de confirmation n'a pas pu être envoyé.");
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
