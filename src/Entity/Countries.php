<?php

namespace App\Entity;

use App\Repository\CountriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountriesRepository::class)]
class Countries
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $countrieName = null;

    #[ORM\Column(length: 255)]
    private ?string $countrieCode = null;

    /**
     * @var Collection<int, Cities>
     */
    #[ORM\OneToMany(targetEntity: Cities::class, mappedBy: 'countries')]
    private Collection $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountrieName(): ?string
    {
        return $this->countrieName;
    }

    public function setCountrieName(string $countrieName): static
    {
        $this->countrieName = $countrieName;

        return $this;
    }

    public function getCountrieCode(): ?string
    {
        return $this->countrieCode;
    }

    public function setCountrieCode(string $countrieCode): static
    {
        $this->countrieCode = $countrieCode;

        return $this;
    }

    /**
     * @return Collection<int, Cities>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(Cities $city): static
    {
        if (!$this->cities->contains($city)) {
            $this->cities->add($city);
            $city->setCountries($this);
        }

        return $this;
    }

    public function removeCity(Cities $city): static
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getCountries() === $this) {
                $city->setCountries(null);
            }
        }

        return $this;
    }
}
