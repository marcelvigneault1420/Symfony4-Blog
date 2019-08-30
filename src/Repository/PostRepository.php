<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */

    public function getAllPosted()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.User', 'u')
            ->addSelect('u')
            ->andWhere('p.IsPosted = 1')
            ->orderBy('p.DatePosted', 'Desc')
            ->getQuery()
            ->getResult();
    }

    public function getPostWithUser($id)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.User', 'u')
            ->addSelect('u')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getUserPosts($id)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.User', 'u')
            ->addSelect('u')
            ->andWhere('u.id = :id')
            ->andWhere('p.IsPosted = 1')
            ->setParameter('id', $id)
            ->orderBy('p.DatePosted', 'Desc')
            ->getQuery()
            ->getResult();
    }

    public function getDraftPosts($id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.idUser = :id')
            ->andWhere('p.IsPosted = 0')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'Desc')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
