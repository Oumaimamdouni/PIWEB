<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlatRepository;

#[ORM\Entity]
class Plat
{
    #[ORM\Column(name: "id_plat", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $idPlat;

    #[ORM\Column(name: "nom_plat", type: "string", length: 50, nullable: false)]
    private $nomPlat;

    #[ORM\Column(name: "description_plat", type: "string", length: 60, nullable: false)]
    private $descriptionPlat;

    #[ORM\Column(name: "prix", type: "float", precision: 10, scale: 0, nullable: false)]
    private $prix;

    #[ORM\Column(name: "quantite", type: "integer", nullable: false)]
    private $quantite;

    #[ORM\Column(name: "nom_categorie", type: "string", length: 50, nullable: false)]
    private $nomCategorie;

    #[ORM\Column(name: "image", type: "string", length: 1500, nullable: false)]
    private $image;

    #[ORM\Column(name: "like_plat", type: "integer", nullable: true)]
    private $likePlat = '0';

    #[ORM\Column(name: "dislike_plat", type: "integer", nullable: true)]
    private $dislikePlat = '0';

    public function getIdPlat(): ?int
    {
        return $this->idPlat;
    }

    public function getNomPlat(): ?string
    {
        return $this->nomPlat;
    }

    public function setNomPlat(string $nomPlat): static
    {
        $this->nomPlat = $nomPlat;

        return $this;
    }

    public function getDescriptionPlat(): ?string
    {
        return $this->descriptionPlat;
    }

    public function setDescriptionPlat(string $descriptionPlat): static
    {
        $this->descriptionPlat = $descriptionPlat;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): static
    {
        $this->nomCategorie = $nomCategorie;

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

    public function getLikePlat(): ?int
    {
        return $this->likePlat;
    }

    public function setLikePlat(?int $likePlat): static
    {
        $this->likePlat = $likePlat;

        return $this;
    }

    public function getDislikePlat(): ?int
    {
        return $this->dislikePlat;
    }

    public function setDislikePlat(?int $dislikePlat): static
    {
        $this->dislikePlat = $dislikePlat;

        return $this;
    }
}
