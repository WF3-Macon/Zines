<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/admin/user/{id}/roles/{role}', name: 'app_admin_role', methods: ['POST'])]
    public function roles(User $user, string $role, UserRepository $userRepository, Request $request): JsonResponse
    {
        $user->setRoles([$role]);
        $userRepository->add($user, true);

        // Envoi un mail à l'utilisateur pour le prévenir d'un changement de rôle
        

        return $this->json(['role' => $role]);
    }
}
