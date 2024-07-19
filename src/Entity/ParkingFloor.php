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
    private ?string $nomination = null;

    /**
     * @var Collection<int, ParkingSpace>
     */
    #[ORM\OneToMany(targetEntity: ParkingSpace::class, mappedBy: 'parkingFloor', orphanRemoval: true)]
    private Collection $parkingSpaces;

    #[ORM\ManyToOne(inversedBy: 'parkingFloors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parks $parks = null;

    public function __construct()
    {
        $this->parkingSpaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomination(): ?string
    {
        return $this->nomination;
    }

    public function setNomination(string $nomination): static
    {
        $this->nomination = $nomination;

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

    public function getParks(): ?Parks
    {
        return $this->parks;
    }

    public function setParks(?Parks $parks): static
    {
        $this->parks = $parks;

        return $this;
    }
}
