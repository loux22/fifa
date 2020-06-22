<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    // /**
    //  * @return Team[] Returns an array of Team objects
    //  */
    
    public function ranking()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.scoring', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function country($country)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.continent = :continent')
            ->setParameter('continent', $country)
            ->orderBy('t.scoring', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countryEuro($country)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.continent = :continent')
            ->setParameter('continent', $country)
            ->orderBy('t.scoring', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    

    /*
    public function findOneBySomeField($value): ?Team
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
