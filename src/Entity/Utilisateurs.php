<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
#[ORM\Entity]
class Utilisateurs implements UserInterface
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\Column(name: "nom", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private $nom;

    #[ORM\Column(name: "prenom", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private $prenom;

    #[ORM\Column(name: "mot_de_passe", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private $motDePasse;

    #[ORM\Column(name: "email", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 50)]
    private $email;

    #[ORM\Column(name: "role", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private $role = 'client';

    #[ORM\Column(name: "image", type: "string", length: 255, nullable: true)]
    private $image;

    #[ORM\Column]
    private bool $blocked = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): static
    {
        $this->blocked = $blocked;

        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role]; // Assuming the user has a single role
    }

    public function getPassword(): string
    {
        return $this->getMotDePasse();
    }

    public function getSalt(): ?string
    {
        // You may not need this if you're using modern password hashing
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Implement this method if you need to erase sensitive data from the user
        // For example, if you're storing plain-text passwords, you can clear them here
    }

    public function __toString():string{
        return $this->nom;
    }
}
