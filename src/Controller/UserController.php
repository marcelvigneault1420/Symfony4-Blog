<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\UserHelper;
use App\Entity\Post;

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

        $userList = $this->getDoctrine()->getRepository(User::class)->searchUser($searchText, $this->getUser()->getId());

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
        try {
            if ($request->getContentType() == 'json' && $request->getContent()) {
                $jsonResponse = json_decode($request->getContent(), true);

                if (count($jsonResponse) > 0 && array_key_exists('idFollow', $jsonResponse)) {
                    $idFollow = intval($jsonResponse["idFollow"]);

                    if ($this->getUser()->getId() != $idFollow) {
                        $flws = $this->getUser()->getFollowers();
                        $toFollow = $this->getDoctrine()->getRepository(User::class)->findOneById($idFollow);

                        if ($toFollow != null) {
                            $isAdded = false;
                            if ($flws->contains($toFollow)) {
                                $this->getUser()->removeFollower($toFollow);
                                $isAdded = false;
                            } else {
                                $this->getUser()->addFollower($toFollow);
                                $isAdded = true;
                            }

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($this->getUser());
                            $em->flush();

                            return new JsonResponse(
                                $isAdded,
                                JsonResponse::HTTP_OK,
                            );
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            $logger->info($th->getMessage());
            return new JsonResponse(
                'error',
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            'Wrong user',
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
     * @Route("/profile/{id<\d+>}", name="profile")
     */
    public function profile($id, UserHelper $userHelper)
    {
        $posts = null;

        $posts = $this->getDoctrine()->getRepository(Post::class)->getUserPosts($id);

        $selectedUser = $this->getDoctrine()->getRepository(User::class)->findOneById($id);

        return $this->render('user/profile.html.twig', [
            'user' => $selectedUser,
            'isFollowing' => $userHelper->isFollowing($this->getUser(), $selectedUser),
            'posts' => $posts,
            'isDraft' => false
        ]);
    }

    /**
     * @Route("/profile/drafts", name="drafts")
     */
    public function drafts()
    {
        $user = $this->getDoctrine()->getRepository(User::class)->getUserWithPosts($this->getUser()->getId(), true);
        $posts = $this->getDoctrine()->getRepository(Post::class)->getDraftPosts($this->getUser()->getId());

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'isDraft' => true
        ]);
    }

    /**
     * @Route("/grantAdmin/{id<\d+>}", name="grant_admin")
     */
    public function grantAdmin($id)
    {
        return $this->redirectToRoute('user_profile', ['id' => $id]);
    }
}
