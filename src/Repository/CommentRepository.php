<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Comment[] Returns an array of Comment objects
     */
    public function findByParrentHash($hash)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parrent_hash = :val')
            ->setParameter('val', $hash)
            ->orderBy('c.added', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Comment[] Returns an array of Comment objects
     */
    public function findByVideoHash($hash)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.video_hash = :val')
            ->setParameter('val', $hash)
            ->orderBy('c.added', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Comment Returns Comment object
     */
    public function findOneByHash($hash): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.hash = :val')
            ->setParameter('val', $hash)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
