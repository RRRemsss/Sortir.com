<?php

namespace App\Controller;


use App\DTO\searchActivityDTO;
use App\Entity\Activity;
use App\Entity\User;
use App\Form\SearchActivityDTOType;
use App\Repository\ActivityRepository;
use App\Repository\StatusRepository;
use App\Services\ActivityArchivate;
use App\Services\activityStatusService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    #[Route('/', name: 'main_home', methods: ['GET','POST'])]
    public function home(Request $request,
                         ActivityRepository $activityRepository,
                        Security $security,
                         ActivityStatusService $activityStatusService,
                        EntityManagerInterface $entityManager,
                        ActivityArchivate $activityArchivate,
    ): Response
    {
        //Verification si l'utilisateur est role-user.
        if (!$security->isGranted('ROLE_USER')){
            return new RedirectResponse($this->generateUrl('/login'));
        }

        //Instanciation de la DTO pour stocker les données de recherche d'activité
        $searchActivityDTO = new searchActivityDTO();

        //Récup de l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur a un campus associé et le définir comme valeur par défaut
        if ($user instanceof User && $user->getCampus()) {
            $defaultCampus = $user->getCampus();
            $searchActivityDTO->campus = $defaultCampus;
        }

        //Création du formulaire SearchActivityDTOType pour lier les données du formulaire soumis à l'objet $searchActivityDTO.
        $mainForm = $this->createForm(SearchActivityDTOType::class, $searchActivityDTO, [
            'default_campus' => $defaultCampus,
        ]);

        $mainForm->handleRequest($request);

        //Archives activité +30 jours
        $activities = $activityArchivate->getActivitiesArchived();

        // Mise à jour des status
        foreach ($activities as $activity) {
            $status = $activityStatusService->getActivityStatus($activity);
            $activity->setStatus($status);
        }

        $entityManager->flush();


        if ($mainForm->isSubmitted()&& $mainForm->isValid()) {
            $activities = $activityRepository->findByNameAndDate($searchActivityDTO);
        }

        return $this->render('main/home.html.twig', [
            'activities' => $activities,
            'form'=>$mainForm,
            'user'=>$user,
        ]);
    }

    #[Route('/{id}/inscription', name: 'sortie_inscription', methods: ['GET','POST'])]
    public function inscription (
                                 Activity $activity,
                                 EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$activity->getUsers()->contains($user)
            && $activity->getUsers()->count() < $activity->getnbMaxIncriptions()
            && $activity->getStatus()->getStatusStatement() == "Ouverte"
            && $activity->getDateLimitInscription() > new \DateTime()) {
            $activity->addUser($user);

        }
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }

    #[Route('/{id}/desinscription', name: 'sortie_desinscription', methods: ['GET','POST'])]
    public function desinscription(
                                   Activity $activity,
                                   EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($activity->getUsers()->contains($user)
            && $activity->getDateTimeStart() > new \DateTime()
            && $activity->getDateLimitInscription() > new \DateTime()) {
            $activity->removeUser($user);
            $entityManager->flush();
        }


        return $this->redirectToRoute('main_home');
    }

}
