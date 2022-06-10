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

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MagazineRepository $magazineRepository): Response
    {
		// Retourne une vue au visiteur dont le contenu est géré dans le fichier "home/index.html.twig"
        return $this->render('home/index.html.twig', [
			// Sélectionne tous les articles et les transfère à la vue dans une variable du nom de "magazines"
            'magazines' => $magazineRepository->findAll()
        ]);
    }

    #[Route('/magazine/{id}', name: 'details_magazine', requirements:['id' => '\d+'])]
    public function details(Magazine $magazine): Response
    {
		/**
		 * Grâce à l'injection de dépendance et à l'ID passée en paramètre de la requête, Doctrine effectue la
		 * sélection en BDD de manière automatique. Si l'ID est inexistant, une erreur 404 est retournée.
		 */

        return $this->render('home/details.html.twig', [
            'magazine' => $magazine
        ]);
    }

    #[Route('/magazine/new', name: 'new_magazine')]
    public function new(Request $request, MagazineRepository $magazineRepository): Response
    {
		// Instancie l'entité qui a permis de générer notre formulaire
        $magazine = new Magazine();

		// Créer le formulaire en passant la classe FormType et l'objet de l'entité
        $form = $this->createForm(MagazineFormType::class, $magazine);

		// Remplira l'entité "$magazine" quand le formulaire sera envoyé
        $form->handleRequest($request);

		// Vérifie que le formulaire est envoyé et que les contraintes de validations sont correctes
        if ($form->isSubmitted() && $form->isValid()) {
			// Insère en BDD les données du formulaire en lui passant l'objet de l'entité.
			// Le second paramètre est à mettre à "true", sinon les données sont seulement persistées et non insérées.
            $magazineRepository->add($magazine, true);

			// Enregistre un message flash à afficher dans le fichier Twig de votre choix
            $this->addFlash('success', 'Le magazine à bien été enregistré');

			// Redirige l'utilisateur vers une autre page selon le nom de la route
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

	#[Route('/magazine/edit/{id}', name: 'edit_magazine', requirements:['id' => '\d+'])]
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
		// Récupère le jeton CSRF généré dans le formulaire
        $tokenCsrf = $request->request->get('token');

		// Vérifie si le jeton est correct avant d'effectuer une suppression
        if ($this->isCsrfTokenValid('delete-magazine-'. $magazine->getId(), $tokenCsrf)) {

			// Supprimer en BDD les données en lui passant l'objet de l'entité.
			// Le second paramètre est à mettre à "true", sinon les données sont seulement persistées et non supprimées.
            $magazineRepository->remove($magazine, true);

			// Enregistre un message flash à afficher dans le fichier Twig de votre choix
            $this->addFlash('success', 'Le magazine à bien été supprimé');
        }

		// Redirige l'utilisateur vers une autre page selon le nom de la route
        return $this->redirectToRoute('app_home');
    }
}
