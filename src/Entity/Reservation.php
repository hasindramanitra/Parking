<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?ParkingSpace $parkingSpace = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reservationDateTime = null;

    #[ORM\Column(length: 255)]
    private ?string $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $reservationEndDateTime = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'reservation')]
    private Collection $payments;

    /**
     * @var Collection<int, FeedBackByUser>
     */
    #[ORM\OneToMany(targetEntity: FeedBackByUser::class, mappedBy: 'reservation')]
    private Collection $feedBackByUsers;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->feedBackByUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getParkingSpace(): ?ParkingSpace
    {
        return $this->parkingSpace;
    }

    public function setParkingSpace(?ParkingSpace $parkingSpace): static
    {
        $this->parkingSpace = $parkingSpace;

        return $this;
    }

    public function getReservationDateTime(): ?\DateTimeInterface
    {
        return $this->reservationDateTime;
    }

    public function setReservationDateTime(\DateTimeInterface $reservationDateTime): static
    {
        $this->reservationDateTime = $reservationDateTime;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getReservationEndDateTime(): ?\DateTimeInterface
    {
        return $this->reservationEndDateTime;
    }

    public function setReservationEndDateTime(?\DateTimeInterface $reservationEndDateTime): static
    {
        $this->reservationEndDateTime = $reservationEndDateTime;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setReservation($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getReservation() === $this) {
                $payment->setReservation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeedBackByUser>
     */
    public function getFeedBackByUsers(): Collection
    {
        return $this->feedBackByUsers;
    }

    public function addFeedBackByUser(FeedBackByUser $feedBackByUser): static
    {
        if (!$this->feedBackByUsers->contains($feedBackByUser)) {
            $this->feedBackByUsers->add($feedBackByUser);
            $feedBackByUser->setReservation($this);
        }

        return $this;
    }

    public function removeFeedBackByUser(FeedBackByUser $feedBackByUser): static
    {
        if ($this->feedBackByUsers->removeElement($feedBackByUser)) {
            // set the owning side to null (unless already changed)
            if ($feedBackByUser->getReservation() === $this) {
                $feedBackByUser->setReservation(null);
            }
        }

        return $this;
    }
}
