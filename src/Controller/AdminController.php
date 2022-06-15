<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function roles(User $user, string $role, UserRepository $userRepository, EmailService $emailService): JsonResponse
    {
        $user->setRoles([$role]);
        $userRepository->add($user, true);

        // Envoi d'un email en utilisant notre service
        $emailService->sendEmail(
            $user->getEmail(), // Destinataire (email)
            'Changement de rôle', // Sujet de l'email
            [
                'template' => 'emails/change_role.html.twig', // Template du mail à utiliser
                'context' => [ // Les variables du templates
                    'name' => $user->getUserIdentifier(),
                    'role' => $role
                ]
            ]
        );

        return $this->json(['role' => $role]);
    }
}
