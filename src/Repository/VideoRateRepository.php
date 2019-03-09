<?php

namespace App\Repository;

use App\Entity\VideoRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VideoRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoRate[]    findAll()
 * @method VideoRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VideoRate::class);
    }

    /**
     * @return VideoRate Returns VideoRate object
     */
    public function findOneByViewer($video_hash, $viewer_username): ?VideoRate
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.video_hash = :hash')
            ->andWhere('l.viewer_username = :username')
            ->setParameter('hash', $video_hash)
            ->setParameter('username', $viewer_username)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countRate($video_hash, $positive)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.video_hash = :hash')
            ->andWhere('l.rate = :rate')
            ->setParameter('hash', $video_hash)
            ->setParameter('rate', $positive)
            ->select('COUNT(l.viewer_username) AS likes')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    // /**
    //  * @return VideoRate[] Returns an array of VideoRate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VideoRate
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
