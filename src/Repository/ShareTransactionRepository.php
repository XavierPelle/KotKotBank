<?php

namespace App\Repository;

use App\Entity\ShareTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShareTransaction>
 *
 * @method ShareTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShareTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShareTransaction[]    findAll()
 * @method ShareTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShareTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareTransaction::class);
    }

//    /**
//     * @return ShareTransaction[] Returns an array of ShareTransaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ShareTransaction
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
