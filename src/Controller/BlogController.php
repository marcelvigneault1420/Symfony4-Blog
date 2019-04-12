<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index()
    {
        $listPosts = $this->getDoctrine()->getRepository(Post::class)->getAllPosted();
        return $this->render('blog/index.html.twig', [
            'listPosts' => $listPosts,
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request)
    {
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
    }

    /**
     * @Route("/post/{id<\d>}", name="post")
     */
    public function post($id)
    {
        if ($id > 0) {
            $post = $this->getDoctrine()->getRepository(Post::class)->getPostWithUser($id);

            if ($post) {
                return $this->render('blog/post.html.twig', ['post' => $post]);
            }
        }

        return $this->redirectToRoute('blog_index');
    }
}
