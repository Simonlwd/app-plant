<?php

namespace App\Repository;

use App\Entity\Plant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plant>
 */
class PlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plant::class);
    }

    public function getPlantsQuery(string $search = '', string $sort = 'p.dutchName', string $direction = 'ASC')
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p');

        if ($search) {
            $qb->andWhere('p.dutchName LIKE :search OR p.latinName LIKE :search')
                ->setParameter('search', "%{$search}%");
        }

        // whitelist van velden voorkomt foute injecties
        $allowedFields = ['p.dutchName', 'p.latinName', 'p.createdAt'];
        if (!in_array($sort, $allowedFields)) {
            $sort = 'p.dutchName';
        }
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        $qb->orderBy($sort, $direction);

        return $qb;
    }

    //    /**
    //     * @return Plant[] Returns an array of Plant objects
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

    //    public function findOneBySomeField($value): ?Plant
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
