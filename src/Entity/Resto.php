<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestoRepository;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity]
class Resto
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id_resto", type: "integer", nullable: false)]
    private $idResto;

    #[ORM\Column(name: "nomR", type: "string", length: 20, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private $nomr;

    #[ORM\Column(name: "adresseR", type: "string", length: 20, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private $adresser;

    #[ORM\Column(name: "numR", type: "integer", nullable: false)]
    #[Assert\NotBlank(message: "The price cannot be blank.")]
    #[Assert\Type(type: 'integer', message: "The price must be an integer.")]
    #[Assert\Positive(message: "The price must be a positive integer.")]
    private $numr;

    public function getIdResto(): ?int
    {
        return $this->idResto;
    }

    public function getNomr(): ?string
    {
        return $this->nomr;
    }

    public function setNomr(string $nomr): static
    {
        $this->nomr = $nomr;

        return $this;
    }

    public function getAdresser(): ?string
    {
        return $this->adresser;
    }

    public function setAdresser(string $adresser): static
    {
        $this->adresser = $adresser;

        return $this;
    }

    public function getNumr(): ?int
    {
        return $this->numr;
    }

    public function setNumr(int $numr): static
    {
        $this->numr = $numr;

        return $this;
    }

    public function __toString(){
        return $this->getNomr();
    }
}
