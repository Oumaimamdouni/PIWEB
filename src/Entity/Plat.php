<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlatRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Plat
{
    #[ORM\Column(name: "id_plat", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $idPlat;

    #[Assert\NotBlank(message: "Nom Plat cannot be blank.")]
    #[ORM\Column(name: "nom_plat", type: "string", length: 10, nullable: false)]
    private $nomPlat;

    #[Assert\NotBlank(message: "Description Plat cannot be blank.")]
    #[Assert\Length(max: 60, maxMessage: "Description Plat cannot be longer than {{ limit }} characters.")]
    #[ORM\Column(name: "description_plat", type: "string", length: 60, nullable: false)]
    private $descriptionPlat;

    #[Assert\NotBlank(message: "Prix cannot be blank.")]
    #[Assert\Positive(message: "Prix must be a positive number.")]
    #[Assert\GreaterThan(value: 0, message: "Prix must be greater than 0.")]
    #[ORM\Column(name: "prix", type: "float", precision: 10, scale: 0, nullable: false)]
    private $prix;

    #[Assert\NotBlank(message: "Quantite cannot be blank.")]
    #[Assert\Type(type: "integer", message: "Quantite must be an integer.")]
    #[Assert\GreaterThan(value: 0, message: "Quantite must be greater than 0.")]
    #[ORM\Column(name: "quantite", type: "integer", nullable: false)]
    private $quantite;

    #[Assert\NotBlank(message: "Nom Categorie cannot be blank.")]
    #[ORM\Column(name: "nom_categorie", type: "string", length: 50, nullable: false)]
    private $nomCategorie;

    #[ORM\Column(name: "image", type: "string", length: 1500, nullable: false)]
    private $image;

    #[Assert\NotBlank(message: "Like Plat cannot be blank.")]
    #[ORM\Column(name: "like_plat", type: "integer", nullable: true)]
    private $likePlat = 0;

    #[Assert\NotBlank(message: "Dislike Plat cannot be blank.")]
    #[ORM\Column(name: "dislike_plat", type: "integer", nullable: true)]
    private $dislikePlat = 0;

    #[ORM\OneToMany(mappedBy: 'idPlat', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

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
    
    public function setNomCategorie(?CategorieMenu $categorie): static
    {
        if ($categorie !== null) {
            $this->nomCategorie = $categorie->getNomCategorie();
        }

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

    public function __toString(): string
    {
        return $this->getNomPlat();
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setIdPlat($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getIdPlat() === $this) {
                $commentaire->setIdPlat(null);
            }
        }

        return $this;
    }
}
