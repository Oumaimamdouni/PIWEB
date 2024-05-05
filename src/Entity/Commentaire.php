<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contenta = null;

    #[ORM\ManyToOne(targetEntity: Plat::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(name: "id_plat", referencedColumnName: "id_plat")]
    private ?Plat $idPlat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenta(): ?string
    {
        return $this->contenta;
    }

    public function setContenta(string $contenta): static
    {
        $this->contenta = $contenta;

        return $this;
    }

    public function getIdPlat(): ?Plat
    {
        return $this->idPlat;
    }

    public function setIdPlat(?Plat $idPlat): static
    {
        $this->idPlat = $idPlat;

        return $this;
    }
}
