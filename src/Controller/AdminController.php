<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\User;
use App\Form\UserImportCsvType;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    /****** Afficher ******/
    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/campus', name: 'campus', methods: ['GET'])]
    public function campus(CampusRepository $campusRepository): Response
    {
        // Récupération des campus par ordre croissant d'ID, et passage à la vue
        $campus = $campusRepository->findBy([], ['id' => 'ASC']);
        return $this->render('admin/campus.html.twig', [
            'campus' => $campus
        ]);
    }

    #[Route('/villes', name: 'villes', methods: ['GET'])]
    public function ville(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findBy([], ['id' => 'ASC']);
        return $this->render('admin/ville.html.twig', [
            'cities' => $cities
        ]);
    }

    #[Route('/users', name: 'users', methods: ['GET'])]
    public function register(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['id' => 'ASC']);
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    /****** Create ******/
    #[Route('/villes/create', name: 'create_city', methods: ['POST'])]
    public function createCity(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response 
    {
        // Création d'une instance de City
        $city = new City();

        // Récupération du nom de la ville et la mettre en majuscule (strtoupper)
        $cityName = strtoupper($request->get('cityName'));

        // Set du nom et du code postal de la ville dans l'objet City
        $city->setCityName($cityName);
        $city->setPostCode($request->get('postCode'));

        // Validation des données avec le ValidatorInterface, affichage des erreurs s'il y en a via un addFlash
        $errors = $validator->validate($city);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('admin_villes');
        }

        // Enregistrement de l'objet City en base
        $entityManager->persist($city);
        $entityManager->flush();

        return $this->redirectToRoute('admin_villes');
    }

    #[Route('/campus/create', name: 'create_campus', methods: ['POST'])]
    public function createCampus(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response 
    {
        $campus = new Campus();
        $campusName = strtoupper($request->get('campusName'));
        $campus->setCampusName($campusName);

        $errors = $validator->validate($campus);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('admin_campus');
        }

        $entityManager->persist($campus);
        $entityManager->flush();

        return $this->redirectToRoute('admin_campus');
    }

    /****** Delete ******/
    #[Route('/users/delete/{id}', name: 'delete_user', methods: ['GET','POST'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        // Suppression d'un utilisateur selon l'id
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/villes/delete/{id}', name: 'delete_city', methods: ['GET','POST'])]
    public function deleteCity(City $city, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($city);
        $entityManager->flush();

        return $this->redirectToRoute('admin_villes');
    }

    #[Route('/campus/delete/{id}', name: 'delete_campus', methods: ['GET','POST'])]
    public function deleteCampus(Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();

        return $this->redirectToRoute('admin_campus');
    }

    /****** Update ******/
    #[Route('/users/update/{id}', name: 'update_users', methods: ['GET','POST'])]
    public function updateUsers(User $user, Request $request, EntityManagerInterface $entityManager): Response 
    {
        // Update des propriétés de l'utilisateur à partir des données de la requete
        $user->setUsername($request->request->get('username'));
        $user->setLastname($request->request->get('lastname'));
        $user->setFirstname($request->request->get('firstname'));
        $user->setPhoneNumber($request->request->get('phoneNumber'));
        $user->setEmail($request->request->get('email'));

        // Gestion des rôles en fonction des données de la requete
        $roleName = $request->request->get('roles');
        $roles = [];
        if ($roleName === 'ROLE_ADMIN') {
            $roles[] = 'ROLE_ADMIN';
        } elseif ($roleName === 'ROLE_ORGANIZER') {
            $roles[] = 'ROLE_ORGANIZER';
        } else {
            $roles[] = 'ROLE_USER';
        }
        $user->setRoles($roles);

        // Update de la propriété 'active' en fonction des données de la requete (bloqué un utilisateur)
        $isActive = (bool) $request->request->get('isActive');
        $user->setActive($isActive);

        // Enregistrement en base
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/villes/update/{id}', name: 'update_city', methods: ['GET','POST'])]
    public function updateCity(City $city, Request $request, EntityManagerInterface $entityManager): Response 
    {
        $newCityName = strtoupper($request->request->get('cityName'));
        $city->setCityName($newCityName);
        $city->setPostCode($request->request->get('postCode'));

        $entityManager->flush();

        return $this->redirectToRoute('admin_villes');
    }

    #[Route('/campus/update/{id}', name: 'update_campus', methods: ['GET','POST'])]
    public function updateCampus(Campus $campus, Request $request, EntityManagerInterface $entityManager): Response 
    {
        $newCampusName = strtoupper($request->request->get('campusName'));
        $campus->setCampusName($newCampusName);

        $entityManager->flush();

        return $this->redirectToRoute('admin_campus');
    }

    /****** CSV ******/
    #[Route('/users/csv', name: 'add_users_csv', methods: ['GET','POST'])]
    public function addUserCsv(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response 
    {
        // Création du formulaire pour importer les CSV
        $form = $this->createForm(UserImportCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier CSV téléchargé
            $uploadedFile = $form->get('csv_file')->getData();

            // Vérification si le fichier a bien été téléchargé
            if ($uploadedFile) {
                // Récupération du chemin d'accès au fichier grace à getRealPath()
                $filePath = $uploadedFile->getRealPath();

                if (file_exists($filePath)) {
                    // Lecture des données du fichier
                    $csvData = array_map('str_getcsv', file($uploadedFile->getPathname()));
                    // Suppression de la première ligne (en tête avec les propriétés)
                    array_shift($csvData);

                        // Boucle foreach sur les colonnes du fichier CSV afin de créer les utilisateurs
                        foreach ($csvData as $column) {
                            $user = new User();

                            // Définition des valeurs par défaut
                            $user->setActive(true);
                            $user->setRoles(["ROLE_USER"]);

                            // On rattache les user aux campus (selon l'id du campus)
                            $campus = $entityManager->getRepository(Campus::class)->findOneBy(['id' => $column[0]]);
                            $user->setCampus($campus);

                            $user->setUsername($column[1]);

                            // Hashage du mot de passe
                            $hashedPassword = $hasher->hashPassword(
                                $user,
                                $column[2]
                            );
                            $user->setPassword($hashedPassword);

                            // Autres propriétés
                            $user->setLastname($column[3]);
                            $user->setFirstname($column[4]);
                            $user->setPhoneNumber($column[5]);
                            $user->setEmail($column[6]);

                            // Enregistrement en base
                            $entityManager->persist($user);
                        }
                        $entityManager->flush();
                        return $this->redirectToRoute('admin_users');
                    } else {
                        // Ajout de différents messages d'erreur
                        $this->addFlash('error', 'le fichier CSV na pas pu etre trouve');
                    }
                } else {
                    $this->addFlash('error', 'le fichier CSV na pas pu etre telecharge');
                }
            }

        // Rendu de la vue avec le formulaire d'import CSV
        return $this->render('admin/csv.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}
