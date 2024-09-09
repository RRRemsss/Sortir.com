<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Status;
use App\Entity\User;
use App\Form\ActivityType;
use App\Form\CancelActivityType;
use App\Form\UpdateActivityType;
use App\Repository\ActivityRepository;
use App\Services\activityStatusService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/sortie', name: 'sortie_')]
class ActivityController extends AbstractController
{

    #[Route('/creation', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request,
                           EntityManagerInterface $entityManager,
                           Security $security): Response
    {
        $statusEnCours = $entityManager->getRepository(Status::class)->findOneBy(['statusStatement' => 'En cours']);
        $reason = ' ';
        // Récupère le campus de l'utilisateur en cours
        $userCampus = $this->getUser()->getCampus();
        // Récupère la session de l'utilisateur
        $user = $security->getUser();

        $activity = new Activity();
        // set Status/Campus/Organizer/Reason lors de la création
        $activity->setStatus($statusEnCours);
        $activity->setCampus($userCampus);
        $activity->setOrganizer($user);
        $activity->setReason($reason);

        $activityForm = $this->createForm(ActivityType::class, $activity);
        $activityForm->handleRequest($request);

        // lors du click sur Ville, recharge la page et affiche les lieux relatifs aux villes sans soumettre le formulaire
        // idem pour les lieux
        /** @var SubmitButton $submitButton */
        $submitButton = $activityForm->get('submit');
        if (!$submitButton->isClicked()) {
            return $this->render('activity/create.html.twig', ['activityForm' => $activityForm, 'activity' => $activity]);
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        



        if ($activityForm->isSubmitted() && $activityForm->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();
            $this->addFlash('success', 'Une sortie a bien été ajoutée');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('activity/create.html.twig', [
            'activityForm' => $activityForm->createView(), 'activity' => $activity
        ]);
    }

    #[Route('/modifier/{id}', name: 'modifier', methods: ['GET', 'POST'])]
    public function update(Request                $request,
                           ActivityRepository     $activityRepository,
                           EntityManagerInterface $entityManager,
                           Activity               $activity): Response
    {

        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $activity->getOrganizer()) {
            return $this->render('/bundles/TwigBundle/Exception/error403.html.twig');
        }

        $updateForm = $this->createForm(UpdateActivityType::class, $activity);
        $updateForm->handleRequest($request);

        /** @var SubmitButton $submitButton */
        $submitButton = $updateForm->get('submit');
        if (!$submitButton->isClicked()) {
            return $this->render('activity/update.html.twig', ['updateActivityForm' => $updateForm, 'activity' => $activity]);
        }


        if ($updateForm->isSubmitted() && $updateForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été mise à jour.');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('activity/update.html.twig', [
            'updateActivityForm' => $updateForm->createView(),
            'activity' => $activity,
        ]);

    }


    #[Route('/annuler/{id}', name: 'cancel', methods: ['GET', 'POST'])]
    public function cancel(Request                $request,
                           EntityManagerInterface $entityManager,
                           ActivityStatusService  $activityStatusService,
                           Activity               $activity): Response
    {

        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $activity->getOrganizer()) {
            return $this->render('/bundles/TwigBundle/Exception/error403.html.twig');
        }

        $cancelForm = $this->createForm(CancelActivityType::class, $activity);
        $cancelForm->handleRequest($request);

        if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
            $status = $activityStatusService->getCancelStatus();
            $activity->setStatus($status);

            $entityManager->persist($activity);
            $entityManager->flush();

            $this->addFlash('success', 'Activité annulée avec succès !');
            return $this->redirectToRoute('main_home')  ;
        }

        return $this->render('activity/cancel.html.twig', [
            'cancelForm' => $cancelForm->createView(),
            'sortie' => $activity
        ]);

    }

    #[Route('/modifier/delete/{id}', name: 'delete_activity', methods: ['GET', 'POST'])]
    public function deleteActivity(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $activity->getId(), $request->request->get('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
            $this->addFlash('success', 'Activité supprimée avec succès!');
        }
        return $this->redirectToRoute('main_home');
    }

    #[Route('/details/{id}', name: 'detail', methods: ['GET'])]
    public function details(Request $request, Activity $activity): Response
    {
        $users = $activity->getUsers();

        return $this->render('activity/details.html.twig', [
            'activity' => $activity,
            'users' => $users
        ]);
    }


}
