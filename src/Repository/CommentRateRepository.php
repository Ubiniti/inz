<?php

namespace App\Repository;

use App\Entity\CommentRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CommentRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentRate[]    findAll()
 * @method CommentRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CommentRate::class);
    }
}
