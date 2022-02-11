<?php

namespace App\Repository;

use App\Entity\Frame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Frame|null find($id, $lockMode = null, $lockVersion = null)
 * @method Frame|null findOneBy(array $criteria, array $orderBy = null)
 * @method Frame[]    findAll()
 * @method Frame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Frame::class);
    }

    /**
     * Fin all the frames order by the name from A to Z
     */
    public function findAllAsc()
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQuery(
            'SELECT f 
            FROM App\Entity\Frame f
            ORDER BY f.framing ASC'

        );

        return $query->getResult();
    }

    // /**
    //  * @return Frame[] Returns an array of Frame objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Frame
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
