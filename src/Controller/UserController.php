<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;

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
     * @Route("/follow", name="follow", methods="POST")
     */
    public function follow(Request $request, LoggerInterface $logger)
    {
        if ($request->getContentType() == 'json' && $request->getContent()) {
            $jsonResponse = json_decode($request->getContent(), true);

            if (count($jsonResponse) > 0 && array_key_exists('idFollow', $jsonResponse)) {
                $idFollow = intval($jsonResponse["idFollow"]);
                $idUser = $this->getUser()->getId();

                $logger->info('UserId:' . $idUser . 'FollowId:' . $idFollow);

                return new JsonResponse(
                    'success',
                    JsonResponse::HTTP_OK
                );
            }
        }

        return new JsonResponse(
            'error',
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/delete/{id<\d+>}", name="delete")
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
