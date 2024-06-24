<?php

namespace App\Entity;

use App\Repository\ParkingFloorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParkingFloorRepository::class)]
class ParkingFloor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 255)]
    private ?string $alphabetName = null;

    /**
     * @var Collection<int, ParkingSpace>
     */
    #[ORM\OneToMany(targetEntity: ParkingSpace::class, mappedBy: 'parkingFloor', orphanRemoval: true)]
    private Collection $parkingSpaces;

    public function __construct()
    {
        $this->parkingSpaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getAlphabetName(): ?string
    {
        return $this->alphabetName;
    }

    public function setAlphabetName(string $alphabetName): static
    {
        $this->alphabetName = $alphabetName;

        return $this;
    }

    /**
     * @return Collection<int, ParkingSpace>
     */
    public function getParkingSpaces(): Collection
    {
        return $this->parkingSpaces;
    }

    public function addParkingSpace(ParkingSpace $parkingSpace): static
    {
        if (!$this->parkingSpaces->contains($parkingSpace)) {
            $this->parkingSpaces->add($parkingSpace);
            $parkingSpace->setParkingFloor($this);
        }

        return $this;
    }

    public function removeParkingSpace(ParkingSpace $parkingSpace): static
    {
        if ($this->parkingSpaces->removeElement($parkingSpace)) {
            // set the owning side to null (unless already changed)
            if ($parkingSpace->getParkingFloor() === $this) {
                $parkingSpace->setParkingFloor(null);
            }
        }

        return $this;
    }
}
