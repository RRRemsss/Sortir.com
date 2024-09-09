<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\ChangeProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// Route pour profil.
#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    // Méthode d'affichage d'un utilisateur.
    #[Route('/{id}', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, User $user): Response
    {
        // Récupération de l'identifiant de l'utilisateur.
        $id = $user->getId();
        // Génération de la vue user/index.html.twig avec les données de l'utilisateur et son identifiant.
        return $this->render('user/index.html.twig', [
            "user" => $user,
            "currentId" => $id,
        ]);
    }

    // Route pour update profil.
    #[Route('/update/{id}', name: 'update', methods: ['GET','POST'])]
    public function update(UserRepository $userRepository, User $user, Request $request, EntityManagerInterface $manager): Response
    {
        // Vérification si l'utilisateur connecté est le même que celui à modifier.
        if ($this->getUser() !== $user) {
            // Redirection vers la page d'accueil si les utilisateurs ne correspondent pas.
            return $this->redirectToRoute('main_home');
        }
        // Création du formulaire de mise à jour avec les données de l'utilisateur.
        $updateForm = $this->createForm(ChangeProfilType::class, $user);
        // Traitement de la requête.
        $updateForm->handleRequest($request);

        // Vérification si le formulaire a été soumis et est valide.
        if ($updateForm->isSubmitted() && $updateForm->isValid()) {
            // Récupération de l'image du formulaire.
            /** @var UploadedFile $imageFile */
            $imageFile = $updateForm->get('imageFile')->getData();

            // Vérification si une image a été téléchargée.
            if ($imageFile) {
                // Mise à jour de l'image de l'utilisateur.
                $user->setImageFile($imageFile);
            }

            //Enregistrement en base de données
            $manager->persist($user);
            $manager->flush();

            // Ajout d'un message flash de succès.
            $this->addFlash(
                'success',
                'Le profil a été modifié avec succès.'
            );

            // Redirection vers la page de l'utilisateur mis à jour.
            return $this->redirectToRoute('user_index', ['id' => $user->getId()]);
        }
        // Récupération de l'image de l'utilisateur.
        $userimage = $userRepository->find($user);
        // Génération de la vue user/update.html.twig avec le formulaire de mise à jour et l'image de l'utilisateur.
        return $this->render('user/update.html.twig', [
            'updateForm' => $updateForm->createView(),
            "userimage" => $userimage
        ]);
    }
}

