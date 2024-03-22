<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findSeriesOnlyReturning(int $offset = null): array|int
    {
        $q = $this->createQueryBuilder('s')
            ->andWhere('s.status = :status')
            ->setParameter(':status', 'returning')
            ->andWhere('s.vote > :vote ')
            ->setParameter(':vote', 8);

        if ($offset || $offset === 0) {
            $q->addOrderBy('s.firstAirDate', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults(20);
        } else {
            $q->select('COUNT(s)');
            return $q->getQuery()->getSingleScalarResult();
        }

        return $q->getQuery()->getResult();
    }

    public function findSeriesWithDql(): array
    {
        $dql = "SELECT s FROM App\Entity\Serie as s WHERE s.status = :status AND s.vote > :vote ORDER BY s.firstAirDate DESC";

        // requete pour avoir le nombre total de series avec ces clauses
        // $dql = "SELECT COUNT(s) FROM App\Entity\Serie as s WHERE s.status = :status AND s.vote > :vote";

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter(':status', 'returning')
            ->setParameter(':vote', 8)
            ->execute();
    }

    public function findSeriesWithSql(int $offset): array
    {
        $rawSql = "SELECT *, COUNT(*) over() as nbMax  FROM serie WHERE status = :status AND vote > :vote ORDER BY first_air_date DESC LIMIT 20 OFFSET :offset";

        $conn = $this->getEntityManager()->getConnection();

        return $conn->prepare($rawSql)
            ->executeQuery([':status' => 'returning', ':vote' => 8, ':offset' => $offset])
            ->fetchAllAssociative();

    }




    //    /**
    //     * @return Serie[] Returns an array of Serie objects
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

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
