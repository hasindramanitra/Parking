<?php

namespace App\Entity;

use App\Repository\CitiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitiesRepository::class)]
class Cities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $citieName = null;

    #[ORM\Column(length: 255)]
    private ?string $citieCode = null;

    #[ORM\ManyToOne(inversedBy: 'cities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Countries $countries = null;

    /**
     * @var Collection<int, Parks>
     */
    #[ORM\OneToMany(targetEntity: Parks::class, mappedBy: 'cities')]
    private Collection $parks;

    public function __construct()
    {
        $this->parks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCitieName(): ?string
    {
        return $this->citieName;
    }

    public function setCitieName(string $citieName): static
    {
        $this->citieName = $citieName;

        return $this;
    }

    public function getCitieCode(): ?string
    {
        return $this->citieCode;
    }

    public function setCitieCode(string $citieCode): static
    {
        $this->citieCode = $citieCode;

        return $this;
    }

    public function getCountries(): ?Countries
    {
        return $this->countries;
    }

    public function setCountries(?Countries $countries): static
    {
        $this->countries = $countries;

        return $this;
    }

    /**
     * @return Collection<int, Parks>
     */
    public function getParks(): Collection
    {
        return $this->parks;
    }

    public function addPark(Parks $park): static
    {
        if (!$this->parks->contains($park)) {
            $this->parks->add($park);
            $park->setCities($this);
        }

        return $this;
    }

    public function removePark(Parks $park): static
    {
        if ($this->parks->removeElement($park)) {
            // set the owning side to null (unless already changed)
            if ($park->getCities() === $this) {
                $park->setCities(null);
            }
        }

        return $this;
    }
}
