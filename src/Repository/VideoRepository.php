<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function findByFilter(string $filter)
    {
        $qb = $this->createQueryBuilder('v')
            ->join('v.categories', 'c')
            ->where("v.title LIKE '%" . $filter . "%'")
            ->orWhere("c.name LIKE '%" . $filter . "%'")
            ->orderBy('v.title');

        return $qb->getQuery()->getResult();
    }

    public function getTitles()
    {
        $qb = $this->createQueryBuilder('v')
            ->select('v.title')
            ->orderBy('v.title');

        return $qb->getQuery()->getResult();
    }
}
