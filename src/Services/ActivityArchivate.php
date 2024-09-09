<?php

namespace App\Services;

use App\Entity\Activity;
use Doctrine\ORM\EntityRepository;

class ActivityArchivate
{
    private $activityRepository;

    public function __construct(EntityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function getActivitiesArchived()
    {
        $activities = $this->activityRepository->findBy([], ['dateTimeStart' => 'DESC']);
        $activities = array_filter($activities, function (Activity $activity) {
            $activityDateLimit = clone $activity->getDateTimeStart();
            $activityDateLimit->modify('+25 days');
            return $activityDateLimit > new \DateTime();
        });

        return $activities;
    }}