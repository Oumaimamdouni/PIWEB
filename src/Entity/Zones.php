<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ZonesRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Zones
{
    #[ORM\Column(name: "zone_id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $zoneId;

    #[ORM\Column(name: "nom", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message:"Nom cannot be blank")]
    private $nom;

    #[ORM\Column(name: "description", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message:"Description cannot be blank")]
    private $description;

    #[ORM\Column(name: "capacity", type: "integer", nullable: false)]
    #[Assert\NotBlank(message:"Capacity cannot be blank")]
    #[Assert\Positive(message:"Capacity must be a positive integer")]
    private $capacity;

    #[ORM\Column(name: "image", type: "string", length: 255, nullable: false)]
    private $image;

    public function getZoneId(): ?int
    {
        return $this->zoneId;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
