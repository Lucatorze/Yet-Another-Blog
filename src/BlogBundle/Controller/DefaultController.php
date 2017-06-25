<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use BlogBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    /**
     * @Route("/en", name="en")
     */
    public function enAction(Request $request)
    {
        $request->getSession()->set('_locale', 'en');
        $from = $request->headers->get('referer');
        return new RedirectResponse($from);
    }

    /**
     * @Route("/fr", name="fr")
     */
    public function frAction(Request $request)
    {
        $request->getSession()->set('_locale', 'fr');
        $from = $request->headers->get('referer');
        return new RedirectResponse($from);
    }
    
    /**
     * @Route("/{page}", name="home", requirements={"page": "\d+"})
     */
    public function indexAction(Request $request, $page=1)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('BlogBundle:Post');
        $posts = $repository->getLastArticles($page);
        $postPathArray = array();

        foreach ($posts as $post){
            $postPathArray[$post->getId()] = $this->slugify($post->getTitle());
        }
        
        return $this->render('BlogBundle:Default:index.html.twig',
            [
                'posts' => $posts,
                'postPathArray' => $postPathArray,
                'page' => $page,
                'nombrePage' => ceil(count($posts)/5)
            ]);
    }
    
    /**
     * @Route("/category/{title}/{page}", name="categoryPostList", requirements={"page": "\d+"})
     */
    public function categoryPostListAction($title, $page=1)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('BlogBundle:Post');
        $posts = $repository->getLastArticlesByCategory($title, $page);
        $postPathArray = array();
        
        foreach ($posts as $post){
            $postPathArray[$post->getId()] = $this->slugify($post->getTitle());
        }
        
        return $this->render('BlogBundle:Default:categoryPostList.html.twig',
            [
                'posts' => $posts,
                'postPathArray' => $postPathArray,
                'catPath' => $title,
                'page' => $page,
                'nombrePage' => ceil(count($posts)/5)
            ]);
    }
    
    /**
     * @Route("/category", name="categoryList")
     */
    public function categoryAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('BlogBundle:Category');
        $category = $repository->findAll();
        $categoryPathArray = array();
        
        foreach ($category as $cat){
            $categoryPathArray[$cat->getId()] = $this->slugify($cat->getTitle());
        }
        
        return $this->render('BlogBundle:Default:category.html.twig',
            [
                'categories' => $category,
                'categoryPathArray' => $categoryPathArray
            ]);
    }
    
    /**
     * @Route("/post/{title}", name="showPost")
     * @Route("/comment/post/{title}", name="postComment")
     * @Method({"GET", "POST"})
     */
    public function showPostAction($title, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repositoryPost = $em->getRepository('BlogBundle:Post');
        $post = $repositoryPost->getPostByTitle($title);
        $repositoryComment = $em->getRepository('BlogBundle:Comment');
        $comments = $repositoryComment->findByPost(array('post' => $post));
        
        $comment = new Comment();
        $form = $this->createForm('BlogBundle\Form\CommentType', $comment);
        $form->handleRequest($request);
                
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            
            if ($form->isSubmitted() && $form->isValid()) {
                $time = new \Datetime('Europe/Paris');
                $em = $this->getDoctrine()->getManager();

                $comment->setPost($post);
                $comment->setAuthor($user);
                $comment->setDate($time);

                $em->persist($comment);
                $em->flush($comment);

                return $this->redirectToRoute('showPost', array('title' => $this->slugify($post->getTitle())));
            }
        }

        return $this->render('BlogBundle:Default:showPost.html.twig',
            [
                'post' => $post,
                'comments' => $comments,
                'form' => $form->createView(),
            ]);
    }
        
    public function slugify($text){
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicated - symbols
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
          return 'n-a';
        }

        return $text;
    }
    
}
