<?php

namespace App\Entity;

use App\Repository\ParkingSpaceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParkingSpaceRepository::class)]
class ParkingSpace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $avalaibilityStatus = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $rate = null;

    #[ORM\ManyToOne(inversedBy: 'parkingSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParkingFloor $parkingFloor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAvalaibilityStatus(): ?bool
    {
        return $this->avalaibilityStatus;
    }

    public function setAvalaibilityStatus(bool $avalaibilityStatus): static
    {
        $this->avalaibilityStatus = $avalaibilityStatus;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getParkingFloor(): ?ParkingFloor
    {
        return $this->parkingFloor;
    }

    public function setParkingFloor(?ParkingFloor $parkingFloor): static
    {
        $this->parkingFloor = $parkingFloor;

        return $this;
    }
}