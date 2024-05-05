<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationEventRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ReservationEvent
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\Column(name: "description", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private $description;

    #[ORM\Column(name: "date", type: "date", nullable: true)]
    #[Assert\NotBlank]
    #[Assert\GreaterThan("today", message: "Date must be after today")]
    private $date;

    #[ORM\ManyToOne(targetEntity: "Evenement")]
    private $idEvenement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getIdEvenement(): ?Evenement
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(?Evenement $idEvenement): static
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }
}
