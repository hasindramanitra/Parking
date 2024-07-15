<?php

namespace App\Entity;

use App\Repository\CitiesRepository;
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
}
