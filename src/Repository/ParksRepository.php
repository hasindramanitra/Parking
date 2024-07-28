<?php

namespace App\Repository;

use App\Entity\Parks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parks>
 */
class ParksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parks::class);
    }


    // public function findAllParkingsByCountrie(): array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->select('p')
    //         ->leftJoin('p.cities', 'pc', 'pc.parks.cities = cities.id')
    //         ->leftJoin('pc.countries', 'cc', 'cc.countries.cities = countries.id')
    //         ->getQuery()
    //         ->getResult();
    // }

    // public function findAllParkingsByCountrieWithCountrieId($id): array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->select('p')
    //         ->leftJoin('p.cities', 'pc', 'pc.parks.cities = cities.id')
    //         ->leftJoin('pc.countries', 'cc', 'cc.countries.cities = countries.id')
    //         ->where('cc.id = :id')
    //         ->setParameter('id', $id)
    //         ->getQuery()
    //         ->getResult();
    // }

    //    /**
    //     * @return Parks[] Returns an array of Parks objects
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

    //    public function findOneBySomeField($value): ?Parks
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
