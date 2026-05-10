<?php

namespace App\Repository;

use App\Entity\Plant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Plant>
 */
class PlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plant::class);
    }

    public function findAllForPagination(PaginatorInterface $paginator, int $page = 1, string $sort = 'p.dutchName', string $direction = 'ASC', string $search = '')
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p');

        if ($search) {
            $qb->andWhere('p.dutchName LIKE :search OR p.latinName LIKE :search')
                ->setParameter('search', "%{$search}%");
        }

        $qb->orderBy($sort, $direction);

        return $paginator->paginate(
            $qb,
            $page,
            10, // items per page
            [
                'sortFieldWhitelist' => ['p.dutchName', 'p.latinName', 'p.createdAt'],
                'distinct' => true,
                'wrap-queries' => true,
            ]
        );
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
