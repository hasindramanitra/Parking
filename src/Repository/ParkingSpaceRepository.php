<?php

namespace App\Repository;

use App\Entity\ParkingSpace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParkingSpace>
 */
class ParkingSpaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParkingSpace::class);
    }


    public function findAllParkingSpaceByCountrieNameAndCityName(string $countrieName, string $cityName):array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id,pc.name,p.identification,p.rate, pf.nomination,pfp.location, pfpc.citieName,pfpc.citieCode, pfpcc.countrieName,pfpcc.countrieCode')
            ->leftJoin('p.Categorie','pc','pc.parkingSpace = p.id')
            ->leftJoin('p.parkingFloor','pf','pf.parkingSpace = p.id')
            ->leftJoin('pf.parks','pfp','pfp.parkingFloors = pf.id')
            ->leftJoin('pfp.cities','pfpc','pfpc.parks = pfp.id')
            ->leftJoin('pfpc.countries', 'pfpcc','pfpcc.cities = pfpc.id')
            ->where('pfpcc.countrieName = :countrieName AND pfpc.citieName = :citieName')
            ->setParameterS(new ArrayCollection(array(
                new Parameter('countrieName',$countrieName),
                new Parameter('citieName', $cityName)
            )))
            ->getQuery()
            ->getResult();
    }

 

    //    /**
    //     * @return ParkingSpace[] Returns an array of ParkingSpace objects
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

    //    public function findOneBySomeField($value): ?ParkingSpace
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
