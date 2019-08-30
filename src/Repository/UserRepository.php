<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */

    public function getUserWithPosts($id, $isPosted)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.posts', 'p')
            ->addSelect('p')
            ->andWhere('u.id = :id')
            ->andWhere('p.IsPosted = :isPosted')
            ->orderBy('p.DatePosted', 'DESC')
            ->setParameter('id', $id)
            ->setParameter('isPosted', $isPosted)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchUser($name, $idCurrUser)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = 'SELECT * FROM User U LEFT JOIN User_user F ON F.user_target = U.id AND F.user_source = :iduser WHERE display_name LIKE :name';
        $stmt = $conn->prepare($sql);
        $stmt->execute(array('name' => '%' . $name . '%', 'iduser' => $idCurrUser));
        return $stmt->fetchAll();
        die;
        /*
        return $this->createQueryBuilder('u')
            ->andWhere('u.DisplayName LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult(); */
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
