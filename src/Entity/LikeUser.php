<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LikeUserRepository;

#[ORM\Entity]
class LikeUser
{
    #[ORM\Column(name: "id_like", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $idLike;

    #[ORM\Column(name: "id_user", type: "integer", nullable: false)]
    private $idUser;

    #[ORM\Column(name: "id_plat", type: "integer", nullable: false)]
    private $idPlat;

    #[ORM\Column(name: "status", type: "string", length: 10, nullable: false)]
    private $status;

    public function getIdLike(): ?int
    {
        return $this->idLike;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdPlat(): ?int
    {
        return $this->idPlat;
    }

    public function setIdPlat(int $idPlat): static
    {
        $this->idPlat = $idPlat;

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
