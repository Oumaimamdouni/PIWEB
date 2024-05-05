<?php

namespace App\Entity;

use App\Entity\Tables;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Reservation
{
    #[ORM\Column(name: "id_R", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $idR;

    #[ORM\Column(name: "id_C", type: "integer", nullable: false)]
    private $idC;

    #[Assert\NotBlank(message: "zone cannot be blank.")]
    #[ORM\ManyToOne(targetEntity: Zones::class)]
    #[ORM\JoinColumn(name: "zone", referencedColumnName: "zone_id")]
    private $zone;

    #[Assert\NotBlank(message: "Dater cannot be blank.")]
    #[Assert\GreaterThanOrEqual(value: "today", message: "Dater must be today's date or a future date.")]
    #[ORM\Column(name: "dateR", type: "date", nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private $dater;

    #[ORM\ManyToOne(targetEntity: Tables::class)]
    #[ORM\JoinColumn(name: "table_id", referencedColumnName: "table_id")]
    private $tableId;

    #[Assert\NotBlank(message: "Status cannot be blank.")]
    #[ORM\Column(name: "status", type: "string", length: 255, nullable: false)]
    private $status = "On Hold";

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

    public function getZone(): ?Zones
    {
        return $this->zone;
    }

    public function setZone(Zones $zone): static
    {
        $this->zone = $zone;

        return $this;
    }

    public function getDater(): ?\DateTime
    {
        return $this->dater;
    }

    public function setDater(\DateTime $dater): static
    {
        $this->dater = $dater;

        return $this;
    }

    public function getTableId(): ?Tables
    {
        return $this->tableId;
    }

    public function setTableId(Tables $tableId): static
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
