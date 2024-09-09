<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
//#[UniqueEntity(fields: ['activityName'], message: 'Il y a déja une activité comportant ce nom')]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champs ne peut être nul')]
    #[Assert\Length(min: 2,max:255,minMessage: 'Le nom de l\'activité doit comporter au moins 2 caractères')]
    private ?string $activityName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date ne peut être nulle')]
    private ?\DateTimeInterface $dateTimeStart = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La durée doit être renseignée')]
    #[Assert\GreaterThanOrEqual(value: 20, message: 'La durée doit être au moins de 20 minutes')]
    #[Assert\LessThanOrEqual(value: 240, message: 'La durée ne peut être supérieure à 4h')]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThan(propertyPath: 'dateTimeStart',message: 'La date de limite d\'inscription doit être inférieure à la date démarrage de la sortie')]
    private ?\DateTimeInterface $dateLimitInscription = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le nombre de participant doit être indiqué')]
    #[Assert\GreaterThanOrEqual(value: 2, message: 'Le nombre de participants doit être au moins de 2')]
    private ?int $nbMaxIncriptions = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Veuillez renseigner une description')]
    #[Assert\Length(min: 5, minMessage: 'La description doit être plus exhaustive')]
    private ?string $activityDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un motif d\'annulation')]
    private ?string $reason = null;

    #[ORM\ManyToOne(inversedBy: 'activity')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'activities')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'activitiesOrganized')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer= null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un campus')]
    private ?Campus $campus = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivityName(): ?string
    {
        return $this->activityName;
    }

    public function setActivityName(string $activityName): static
    {
        $this->activityName = $activityName;

        return $this;
    }

    public function getDateTimeStart(): ?\DateTimeInterface
    {
        return $this->dateTimeStart;
    }

    public function setDateTimeStart(\DateTimeInterface $dateTimeStart): static
    {
        $this->dateTimeStart = $dateTimeStart;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDateLimitInscription(): ?\DateTimeInterface
    {
        return $this->dateLimitInscription;
    }

    public function setDateLimitInscription(\DateTimeInterface $dateLimitInscription): static
    {
        $this->dateLimitInscription = $dateLimitInscription;

        return $this;
    }

    public function getNbMaxIncriptions(): ?int
    {
        return $this->nbMaxIncriptions;
    }

    public function setNbMaxIncriptions(int $nbMaxIncriptions): static
    {
        $this->nbMaxIncriptions = $nbMaxIncriptions;

        return $this;
    }

    public function getActivityDescription(): ?string
    {
        return $this->activityDescription;
    }

    public function setActivityDescription(string $activityDescription): static
    {
        $this->activityDescription = $activityDescription;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addActivity($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeActivity($this);
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): void
    {
        $this->organizer = $organizer;
    }
}
