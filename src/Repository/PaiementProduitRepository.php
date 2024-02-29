<?php

namespace App\Repository;

use App\Entity\PaiementProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaiementProduit>
 *
 * @method PaiementProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaiementProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaiementProduit[]    findAll()
 * @method PaiementProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaiementProduit::class);
    }

//    /**
//     * @return PaiementProduit[] Returns an array of PaiementProduit objects
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

//    public function findOneBySomeField($value): ?PaiementProduit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
