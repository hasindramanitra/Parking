<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $width = null;

    #[ORM\Column]
    private ?float $length = null;

    /**
     * @var Collection<int, ParkingSpace>
     */
    #[ORM\OneToMany(targetEntity: ParkingSpace::class, mappedBy: 'Categorie')]
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

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(float $length): static
    {
        $this->length = $length;

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
            $parkingSpace->setCategorie($this);
        }

        return $this;
    }

    public function removeParkingSpace(ParkingSpace $parkingSpace): static
    {
        if ($this->parkingSpaces->removeElement($parkingSpace)) {
            // set the owning side to null (unless already changed)
            if ($parkingSpace->getCategorie() === $this) {
                $parkingSpace->setCategorie(null);
            }
        }

        return $this;
    }
}
