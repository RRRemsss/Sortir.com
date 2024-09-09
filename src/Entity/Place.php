<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['liste_ville'])]//
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['liste_ville'])] //
    private ?string $placeName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['liste_ville'])]//
    private ?string $street = null;

    #[ORM\Column]
    #[Groups(['liste_ville'])]//
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(['liste_ville'])]//
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'place')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['liste_ville'])]//
    private ?City $cities = null;

    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'place')]
    private Collection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlaceName(): ?string
    {
        return $this->placeName;
    }

    public function setPlaceName(string $placeName): static
    {
        $this->placeName = $placeName;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCities(): ?City
    {
        return $this->cities;
    }

    public function setCities(?City $cities): static
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setPlace($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getPlace() === $this) {
                $activity->setPlace(null);
            }
        }

        return $this;
    }
}
