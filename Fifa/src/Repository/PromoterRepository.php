<?php

namespace App\Repository;

use App\Entity\Promoter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Promoter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promoter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promoter[]    findAll()
 * @method Promoter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promoter::class);
    }

    // /**
    //  * @return Promoter[] Returns an array of Promoter objects
    //  */
    
    public function findLastPromoterYear($tournament)
    {
        return $this->createQueryBuilder('p')
            ->select("p.years as years")
            ->andWhere('p.tournament = :val')
            ->setParameter('val', $tournament)
            ->orderBy('p.years', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Promoter
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
