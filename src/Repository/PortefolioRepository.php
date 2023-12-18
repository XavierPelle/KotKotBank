<?php

namespace App\Repository;

use App\Entity\Portefolio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Portefolio>
 *
 * @method Portefolio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Portefolio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Portefolio[]    findAll()
 * @method Portefolio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortefolioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portefolio::class);
    }

//    /**
//     * @return Portefolio[] Returns an array of Portefolio objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Portefolio
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
