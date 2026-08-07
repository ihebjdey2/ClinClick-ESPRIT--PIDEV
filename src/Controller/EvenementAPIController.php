<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evenement;
use App\Repository\CategoryRepository;
use App\Repository\EvenementRepository;
use App\Repository\ParticiperRepository;
use App\Service\EventImageUploader;
use DateTimeImmutable;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/API')]
final class EvenementAPIController extends AbstractController
{
    private const CSRF_TOKEN_ID = 'api-event';

    #[Route('/evenement', name: 'api_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $repository): JsonResponse
    {
        return $this->json(array_map(
            fn (Evenement $event): array => $this->normalizeEvent($event),
            $repository->findBy([], ['date' => 'ASC'])
        ));
    }

    #[Route('/addMobile', name: 'api_evenement_new', methods: ['POST'])]
    public function create(
        Request $request,
        EvenementRepository $repository,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->denyUnlessValidApiCsrf($request);
        $payload = $this->decodePayload($request);
        $category = $categoryRepository->find((int) ($payload['categoryId'] ?? 0));

        try {
            $date = new DateTimeImmutable((string) ($payload['date'] ?? ''));
        } catch (Exception) {
            return $this->json(['error' => 'La date est invalide.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $event = (new Evenement())
            ->setTitre(trim((string) ($payload['titre'] ?? '')))
            ->setDescription(trim((string) ($payload['description'] ?? '')))
            ->setDate($date)
            ->setCategory($category)
            ->setImage('default-event.svg');

        if (($errors = $validator->validate($event))->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $repository->save($event, true);

        return $this->json($this->normalizeEvent($event), Response::HTTP_CREATED);
    }

    #[Route('/delRecAPI/{id}', name: 'api_event_delete', methods: ['DELETE'])]
    public function delete(
        Request $request,
        #[MapEntity] Evenement $event,
        EvenementRepository $repository,
        ParticiperRepository $participationRepository,
        EventImageUploader $imageUploader
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->denyUnlessValidApiCsrf($request);
        if ($participationRepository->countForEvent($event) > 0) {
            return $this->json(
                ['error' => 'Cet événement possède des participants et ne peut pas être supprimé.'],
                Response::HTTP_CONFLICT
            );
        }

        $image = $event->getImage();
        $repository->remove($event, true);
        $imageUploader->remove($image);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/upRecAPI/{id}', name: 'api_evenement_update', methods: ['PATCH'])]
    public function update(
        Request $request,
        #[MapEntity] Evenement $event,
        EvenementRepository $repository,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->denyUnlessValidApiCsrf($request);
        $payload = $this->decodePayload($request);

        if (array_key_exists('titre', $payload)) {
            $event->setTitre(trim((string) $payload['titre']));
        }
        if (array_key_exists('description', $payload)) {
            $event->setDescription(trim((string) $payload['description']));
        }
        if (array_key_exists('categoryId', $payload)) {
            $event->setCategory($categoryRepository->find((int) $payload['categoryId']));
        }
        if (array_key_exists('date', $payload)) {
            try {
                $event->setDate(new DateTimeImmutable((string) $payload['date']));
            } catch (Exception) {
                return $this->json(['error' => 'La date est invalide.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if (($errors = $validator->validate($event))->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $repository->save($event, true);

        return $this->json($this->normalizeEvent($event));
    }

    #[Route('/recAPI', name: 'api_evenement_detail', methods: ['GET'])]
    public function detail(Request $request, EvenementRepository $repository): JsonResponse
    {
        $event = $repository->find($request->query->getInt('id'));
        if (!$event instanceof Evenement) {
            return $this->json(['error' => 'Événement introuvable.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->normalizeEvent($event));
    }

    #[Route('/csrf-token', name: 'api_event_csrf_token', methods: ['GET'])]
    public function csrfToken(CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $response = $this->json(['token' => $csrfTokenManager->getToken(self::CSRF_TOKEN_ID)->getValue()]);
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-store');

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodePayload(Request $request): array
    {
        $payload = json_decode($request->getContent(), true);

        return is_array($payload) ? $payload : $request->request->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeEvent(Evenement $event): array
    {
        return [
            'id' => $event->getId(),
            'titre' => $event->getTitre(),
            'description' => $event->getDescription(),
            'date' => $event->getDate()?->format('Y-m-d'),
            'image' => $event->getImage(),
            'category' => $event->getCategory() === null ? null : [
                'id' => $event->getCategory()?->getId(),
                'nom' => $event->getCategory()?->getNom(),
            ],
        ];
    }

    private function denyUnlessValidApiCsrf(Request $request): void
    {
        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, (string) $request->headers->get('X-CSRF-Token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF API invalide.');
        }
    }

    private function validationErrorResponse(ConstraintViolationListInterface $errors): JsonResponse
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $this->json(['errors' => $messages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
