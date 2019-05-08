<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(Request $request)
    {
        $searchText = $request->query->get('search-bar-field');

        $userList = $this->getDoctrine()->getRepository(User::class)->searchUser($searchText);


        return $this->render('user/list.html.twig', [
            'userList' => $userList,
            'searchText' => $searchText,
        ]);
    }

    /**
     * @Route("/delete", name="delete")
     */
    public function delete($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \Exception('This will never be executed');
    }

    /**
     * @Route("/profile/{id<\d>}", name="profile")
     */
    public function profile($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->getUserWithPosts($id);

        return $this->render('user/profile.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/grantAdmin/{id<\d>}", name="grant_admin")
     */
    public function grantAdmin($id)
    {
        return $this->redirectToRoute('user_profile', ['id' => $id]);
    }
}
