<?php

namespace App\Repository;

use App\Entity\LikeUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateurs>
 *
 * @method LikeUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeUser[]    findAll()
 * @method LikeUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeUser::class);
    }

}
