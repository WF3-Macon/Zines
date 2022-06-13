<?php

namespace App\Controller;

use App\Entity\Magazine;
use App\Form\MagazineFormType;
use App\Repository\MagazineRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MagazineRepository $magazineRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $magazines = $paginatorInterface->paginate(
            $magazineRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('home/index.html.twig', [
            'magazines' => $magazines
        ]);
    }

    #[Route('/magazine/{id}', name: 'details_magazine', requirements:['id' => '\d+'])]
    public function details(Magazine $magazine): Response
    {
        return $this->render('home/details.html.twig', [
            'magazine' => $magazine
        ]);
    }

    #[Route('/magazine/new', name: 'new_magazine')]
    public function new(Request $request, MagazineRepository $magazineRepository): Response
    {
        $magazine = new Magazine();
        $form = $this->createForm(MagazineFormType::class, $magazine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $magazineRepository->add($magazine, true);
            $this->addFlash('success', 'Le magazine à bien été enregistré');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/magazine/edit/{id}', name: 'edit_magazine', requirements: ['id' => '\d+'])]
    public function edit(Magazine $magazine, Request $request, MagazineRepository $magazineRepository): Response
    {
        $form = $this->createForm(MagazineFormType::class, $magazine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $magazineRepository->add($magazine, true);
            $this->addFlash('success', 'Le magazine à bien été modifié');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/magazine/delete/{id}', name:'magazine_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Magazine $magazine, Request $request, MagazineRepository $magazineRepository): RedirectResponse
    {
        $tokenCsrf = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-magazine-'. $magazine->getId(), $tokenCsrf)) {
            $magazineRepository->remove($magazine, true);
            $this->addFlash('success', 'Le magazine à bien été supprimé');
        }

        return $this->redirectToRoute('app_home');
    }
}
