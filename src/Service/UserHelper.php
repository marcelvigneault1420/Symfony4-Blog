<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class UserHelper
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function isFollowing($user, $follower): bool
    {
        return $user->getFollowers()->contains($follower);
    }

    public function getFollowingPosts($userId): array
    {
        $query = $this->em->createQuery('SELECT p FROM App\Entity\Post p
        JOIN p.User u WHERE p.IsPosted = true AND u.id IN
        (SELECT f.id FROM App\Entity\User x JOIN x.followers f WHERE x.id = ' . $userId . ') ORDER BY p.DatePosted DESC')
            ->setFirstResult(0)
            ->setMaxResults(10);
        $posts = $query->getResult();

        return $posts;
    }
}
