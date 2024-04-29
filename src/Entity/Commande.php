<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Resto;
use App\Repository\CommandeRepository;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "numC", type: "integer", length: 20, nullable: false)]
    private $numc;

    #[ORM\Column(name: "adresseC", type: "string", length: 20, nullable: false)]
    #[Assert\NotBlank(message: "The address cannot be blank.")]
    #[Assert\Length(max: 20, maxMessage: "The address must be at most {{ limit }} characters.")]
    private $adressec;

    #[ORM\Column(name: "panier", type: "string", length: 20, nullable: false)]
    #[Assert\NotBlank(message: "The panier cannot be blank.")]
    #[Assert\Length(max: 20, maxMessage: "The panier must be at most {{ limit }} characters.")]
    private $panier;

    #[ORM\Column(name: "price", type: "integer", nullable: false)]
    #[Assert\NotBlank(message: "The price cannot be blank.")]
    #[Assert\Type(type: 'integer', message: "The price must be an integer.")]
    #[Assert\Positive(message: "The price must be a positive integer.")]
    private $price;

    #[ORM\Column(name: "status", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "The status cannot be blank.")]
    #[Assert\Length(max: 255, maxMessage: "The status must be at most {{ limit }} characters.")]
    private $status = 'On Hold';

    #[ORM\ManyToOne(targetEntity: Resto::class)]
    #[ORM\JoinColumn(name: "id_resto", referencedColumnName: "id_resto")]
    private $idresto;


    public function getNumc(): ?int
    {
        return $this->numc;
    }

    public function getAdressec(): ?string
    {
        return $this->adressec;
    }

    public function setAdressec(string $adressec): static
    {
        $this->adressec = $adressec;

        return $this;
    }

    public function getPanier(): ?string
    {
        return $this->panier;
    }

    public function setPanier(string $panier): static
    {
        $this->panier = $panier;

        return $this;
    }

    public function getIdresto(): ?Resto
    {
        return $this->idresto;
    }

    public function setIdresto(Resto $idresto): static
    {
        $this->idresto = $idresto;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
