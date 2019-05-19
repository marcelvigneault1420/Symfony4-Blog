<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Service\UserHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(AuthorizationCheckerInterface $authChecker, UserHelper $uHelper)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $posts = $uHelper->getFollowingPosts($this->getUser()->getId());

            return $this->render('blog/index.html.twig', [
                'listPosts' => $posts,
            ]);
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $post = new Post();
            $form = $this->createForm(PostType::class, $post);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post = $form->getData();
                $agreeTerms = $form->get('agreeTerms')->getData();

                if ($agreeTerms) {
                    $em = $this->getDoctrine()->getManager();
                    $post->setUser($this->getUser());
                    $isDraft = $form->get('isDraft')->getData();
                    if ($isDraft == false) {
                        $post->setDatePosted(new \DateTime('now'));
                    }
                    $post->setIsPosted($isDraft == false);
                    $em->persist($post);
                    $em->flush();

                    return $this->redirectToRoute('blog_post', ['id' => $post->getId()]);
                } else {
                    $error = new FormError("Please agree the terms");
                    $form->get('agreeTerms')->addError($error);
                }
            }

            return $this->render('blog/add.html.twig', array('form' => $form->createView()));
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/post/{id<\d+>}", name="post")
     */
    public function post($id, UserHelper $userHelper)
    {
        if ($id > 0) {
            $post = $this->getDoctrine()->getRepository(Post::class)->getPostWithUser($id);

            if ($post && ($post->getUser()->getId() == $this->getUser()->getId() || $post->getIsPosted())) {
                return $this->render('blog/post.html.twig', ['post' => $post, 'isFollowing' => $userHelper->isFollowing($this->getUser(), $post->getUser())]);
            }
        }

        return $this->redirectToRoute('blog_index');
    }

    /**
     * @Route("/posts", name="posts", methods="GET")
     */
    public function posts(Request $request, LoggerInterface $logger, AuthorizationCheckerInterface $authChecker, UserHelper $uHelper)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $idFollower = $request->query->get('id');

            $id = is_numeric($idFollower) ? intval($idFollower) : -2;
            if ($id >= -1) {
                $posts = null;

                if ($id == -1) {
                    $posts = $this->getDoctrine()->getRepository(Post::class)->getAllPosted();
                } elseif ($id == 0) {
                    $posts = $uHelper->getFollowingPosts($this->getUser()->getId());
                } else {
                    $posts = $this->getDoctrine()->getRepository(Post::class)->getUserPosts($id);
                }

                if ($posts != null) {
                    return new JsonResponse(
                        ['htmlview' => $this->renderView('blog/_articles.html.twig', [
                            'listPosts' => $posts,
                        ])],
                        JsonResponse::HTTP_OK
                    );
                }
            }
        }

        return new JsonResponse(
            'error',
            JsonResponse::HTTP_BAD_REQUEST
        );
    }
}
