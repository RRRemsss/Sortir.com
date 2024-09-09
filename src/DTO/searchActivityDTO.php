<?php

namespace App\DTO;

use App\Entity\Campus;
use DateTimeInterface;

class searchActivityDTO
{
    public ?Campus $campus;
    public ?string $activityName;
    public ?\DateTimeInterface $filterDateMin = null;
    public ?\DateTimeInterface $filterDateMax = null;

    public ?bool $checkboxOrganizer;
    public ?bool $checkBoxBooked;
    public ?bool $checkBoxNotBooked;
    public ?bool $activityPassed;

    }