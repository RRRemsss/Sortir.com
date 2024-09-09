<?php

namespace App\Services;

use App\Entity\Activity;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;

class activityStatusService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getActivityStatus(Activity $activity): Status
    {
        $now = new \DateTime();
        $tomorrow = new \DateTime('tomorrow');

        if ($activity->getStatus()->getStatusStatement() === 'Annulée') {
            return $this->getCancelStatus();
        }

        if ($activity->getUsers()->count() == $activity->getNbMaxIncriptions()) {
            return $this->getCompletedStatus();
        }

        if ($now > $activity->getDateTimeStart()) {
            return $this->getPassedActivity();
        }

        if ($activity->getDateTimeStart() == $now && $now <= $tomorrow) {
            return $this->getInProgressStatus();
        }

        if ($activity->getDateLimitInscription() < $now) {
            return $this->getClosedStatus();
        }


        if ($activity->getDateLimitInscription() > $now && $now < $activity->getDateTimeStart()) {
            return $this->getOpenedStatus();
        }

        return $this->getCancelStatus();
    }

    public function getOpenedStatus(): Status
    {
        $statusRepository = $this->entityManager->getRepository(Status::class);
        $openedStatus = $statusRepository->findOneBy(['statusStatement' => 'Ouverte']);

        if (!$openedStatus) {
            throw new \RuntimeException('Le status "Ouverte" n\'existe pas dans la base de données');
        }

        return $openedStatus;
    }

    public function getCompletedStatus(): Status
    {
        $statusRepository = $this->entityManager->getRepository(Status::class);
        $completedStatus = $statusRepository->findOneBy(['statusStatement' => 'Complète']);

        if (!$completedStatus) {
            throw new \RuntimeException('Le status "Complète" n\'existe pas dans la base de données');
        }

        return $completedStatus;
    }

    public function getInProgressStatus(): Status
    {
        $statusRepository = $this->entityManager->getRepository(Status::class);
        $inProgressStatus = $statusRepository->findOneBy(['statusStatement' => 'En cours']);

        if (!$inProgressStatus) {
            throw new \RuntimeException('Le status "En cours" n\'existe pas dans la base de données');
        }

        return $inProgressStatus;
    }

    public function getPassedActivity(): Status
    {
        $statusRepository = $this->entityManager->getRepository(Status::class);
        $passedStatus = $statusRepository->findOneBy(['statusStatement' => 'Passée']);

        if (!$passedStatus) {
            throw new \RuntimeException('Le status "Passée" n\'existe pas dans la base de données');
        }

        return $passedStatus;
    }

    public function getClosedStatus(): Status
    {
        $statusRepository = $this->entityManager->getRepository(Status::class);
        $closedStatus = $statusRepository->findOneBy(['statusStatement' => 'Fermée']);

        if (!$closedStatus) {
            throw new \RuntimeException('Le status "Fermée" n\'existe pas dans la base de données');
        }

        return $closedStatus;
    }

    public function getCancelStatus()
    {
        $statusRepository = $this->entityManager->getRepository(Status::class);
        $cancelStatus = $statusRepository->findOneBy(['statusStatement' => 'Annulée']);

        if (!$cancelStatus) {
            throw new \RuntimeException('Le status "Annulée" n\'existe pas dans la base de données');
        }

        return $cancelStatus;
    }

}