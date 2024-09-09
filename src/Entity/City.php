<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    //testJson
    #[Groups(['liste_ville'])] //
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['liste_ville'])] //
    #[Assert\NotBlank(message: 'Veuillez renseigner une ville')]
    private ?string $cityName = null;

    #[ORM\Column(length: 30)]
    #[Groups(['liste_ville'])] //
    #[Assert\NotBlank(message: 'Veuillez renseigner un code postal')]
    #[Assert\Length(min: 5, max: 5, exactMessage: 'Le code postal doit comporter 5 chiffres')]
    #[Assert\Regex(pattern: "/^[0-9]+$/", message: 'Le code postal doit contenir uniquement des chiffres')]
    private ?string $postCode = null;

    #[ORM\OneToMany(targetEntity: Place::class, mappedBy: 'cities')]
    // Ne pas rajouter le groupe ici
    private Collection $place;

    public function __construct()
    {
        $this->place = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(string $cityName): static
    {
        $this->cityName = $cityName;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): static
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlace(): Collection
    {
        return $this->place;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->place->contains($place)) {
            $this->place->add($place);
            $place->setCities($this);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        if ($this->place->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getCities() === $this) {
                $place->setCities(null);
            }
        }

        return $this;
    }
}
