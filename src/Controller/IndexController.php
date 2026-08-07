<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(DashboardService $dashboardService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('index/index.html.twig', [
            'dashboard' => $dashboardService->getFor($user),
        ]);
    }
}
