<?php

namespace App\Repository;

use App\Entity\Resto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateurs>
 *
 * @method Resto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resto[]    findAll()
 * @method Resto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resto::class);
    }

}
