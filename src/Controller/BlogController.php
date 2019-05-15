<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Service\UserHelper;

/**
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $listPosts = $this->getDoctrine()->getRepository(Post::class)->getAllPosted();
            return $this->render('blog/index.html.twig', [
                'listPosts' => $listPosts,
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

            if ($post && $post->getIsPosted()) {
                return $this->render('blog/post.html.twig', ['post' => $post, 'isFollowing' => $userHelper->isFollowing($this->getUser(), $post->getUser())]);
            }
        }

        return $this->redirectToRoute('blog_index');
    }
}
