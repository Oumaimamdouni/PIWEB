<?php

namespace App\Repository;

use App\Entity\CategorieMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateurs>
 *
 * @method CategorieMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieMenu[]    findAll()
 * @method CategorieMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieMenu::class);
    }

}
