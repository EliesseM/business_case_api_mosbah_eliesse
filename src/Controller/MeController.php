<?php

// src/Controller/MeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MeController extends AbstractController
{
    #[Route('/api/users/me', name: 'api_users_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser(); // récupère l'utilisateur connecté
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        return $this->json($user, 200, [], ['groups' => ['user:read']]);
    }
}
