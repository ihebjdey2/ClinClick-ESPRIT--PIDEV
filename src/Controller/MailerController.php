<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('mailer/index.html.twig');
    }

    #[Route('/email', name: 'app_mailer_send', methods: ['POST'])]
    public function sendEmail(
        Request $request,
        MailerInterface $mailer,
        string $mailerFrom
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('send-test-email', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        try {
            $mailer->send((new Email())
                ->from($mailerFrom)
                ->to((string) $user->getEmail())
                ->subject('E-mail de test CliniClic')
                ->text("La configuration d'envoi d'e-mails de CliniClic fonctionne."));
            $this->addFlash('success', "L'e-mail de test a été envoyé à votre adresse.");
        } catch (TransportExceptionInterface) {
            $this->addFlash('warning', "L'e-mail n'a pas pu être envoyé. Vérifiez la configuration MAILER_DSN.");
        }

        return $this->redirectToRoute('app_mailer');
    }
}
