<?php

namespace App\Service;

class UserHelper
{
    public function isFollowing($user, $follower): bool
    {
        return $user->getFollowers()->contains($follower);
    }
}
