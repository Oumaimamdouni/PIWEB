<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;

#[ORM\Entity]
class Reservation
{
    #[ORM\Id]
    #[ORM\Column(name: "id_R", type: "integer", nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $idR;

    #[ORM\Column(name: "id_C", type: "integer", nullable: false)]
    private $idC;

    #[ORM\Column(name: "zone", type: "string", length: 50, nullable: false)]
    private $zone;

    #[ORM\Column(name: "dateR", type: "date", nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private $dater = 'CURRENT_TIMESTAMP';

    #[ORM\Column(name: "table_id", type: "integer", nullable: false)]
    private $tableId;

    #[ORM\Column(name: "status", type: "string", length: 255, nullable: false)]
    private $status;

    public function getIdR(): ?int
    {
        return $this->idR;
    }

    public function getIdC(): ?int
    {
        return $this->idC;
    }

    public function setIdC(int $idC): static
    {
        $this->idC = $idC;

        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): static
    {
        $this->zone = $zone;

        return $this;
    }

    public function getDater(): ?\DateTimeInterface
    {
        return $this->dater;
    }

    public function setDater(\DateTimeInterface $dater): static
    {
        $this->dater = $dater;

        return $this;
    }

    public function getTableId(): ?int
    {
        return $this->tableId;
    }

    public function setTableId(int $tableId): static
    {
        $this->tableId = $tableId;

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
