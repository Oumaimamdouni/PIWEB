<?php

namespace App\Repository;

use App\Entity\Zones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateurs>
 *
 * @method Zones|null find($id, $lockMode = null, $lockVersion = null)
 * @method Zones|null findOneBy(array $criteria, array $orderBy = null)
 * @method Zones[]    findAll()
 * @method Zones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZonesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zones::class);
    }

}
