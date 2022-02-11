<?php

namespace App\Repository;

use App\Entity\PaintTech;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaintTech|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaintTech|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaintTech[]    findAll()
 * @method PaintTech[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaintTechRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaintTech::class);
    }

    // /**
    //  * @return PaintTech[] Returns an array of PaintTech objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaintTech
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
