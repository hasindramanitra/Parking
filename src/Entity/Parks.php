<?php

namespace App\Entity;

use App\Repository\ParksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParksRepository::class)]
class Parks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'parks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cities $cities = null;

    /**
     * @var Collection<int, ParkingFloor>
     */
    #[ORM\OneToMany(targetEntity: ParkingFloor::class, mappedBy: 'parks', orphanRemoval: true)]
    private Collection $parkingFloors;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    public function __construct()
    {
        $this->parkingFloors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCities(): ?Cities
    {
        return $this->cities;
    }

    public function setCities(?Cities $cities): static
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * @return Collection<int, ParkingFloor>
     */
    public function getParkingFloors(): Collection
    {
        return $this->parkingFloors;
    }

    public function addParkingFloor(ParkingFloor $parkingFloor): static
    {
        if (!$this->parkingFloors->contains($parkingFloor)) {
            $this->parkingFloors->add($parkingFloor);
            $parkingFloor->setParks($this);
        }

        return $this;
    }

    public function removeParkingFloor(ParkingFloor $parkingFloor): static
    {
        if ($this->parkingFloors->removeElement($parkingFloor)) {
            // set the owning side to null (unless already changed)
            if ($parkingFloor->getParks() === $this) {
                $parkingFloor->setParks(null);
            }
        }

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
}
