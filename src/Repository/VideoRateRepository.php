<?php

namespace App\Repository;

use App\Entity\Video;
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

    public function countRate(Video $video, $rate)
    {
        return $this->createQueryBuilder('vr')
            ->andWhere('vr.video = :video')
            ->andWhere('vr.rate = :rate')
            ->setParameter('video', $video)
            ->setParameter('rate', $rate)
            ->select('COUNT(vr.author) AS result')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
