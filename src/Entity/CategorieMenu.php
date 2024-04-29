<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategorieMenuRepository;

#[ORM\Entity]
class CategorieMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_categorie", type: "integer", nullable: false)]
    private $idCategorie;

    #[ORM\Column(name: "nom_categorie", type: "string", length: 50, nullable: false)]
    private $nomCategorie;

    #[ORM\Column(name: "description_categorie", type: "string", length: 60, nullable: false)]
    private $descriptionCategorie;

    #[ORM\Column(name: "image_categorie", type: "string", length: 2500, nullable: false)]
    private $imageCategorie;

    public function getIdCategorie(): ?int
    {
        return $this->idCategorie;
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

    public function getDescriptionCategorie(): ?string
    {
        return $this->descriptionCategorie;
    }

    public function setDescriptionCategorie(string $descriptionCategorie): static
    {
        $this->descriptionCategorie = $descriptionCategorie;

        return $this;
    }

    public function getImageCategorie(): ?string
    {
        return $this->imageCategorie;
    }

    public function setImageCategorie(string $imageCategorie): static
    {
        $this->imageCategorie = $imageCategorie;

        return $this;
    }
}
