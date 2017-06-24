<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


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
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('BlogBundle:Post');
        $posts = $repository->getLastArticles();
        $postPathArray = array();
        
        foreach ($posts as $post){
            $postPathArray[$post->getId()] = $this->slugify($post->getTitle());
        }
        
        return $this->render('BlogBundle:Default:index.html.twig',
            [
                'posts' => $posts,
                'postPathArray' => $postPathArray
            ]);
    }
    
    /**
     * @Route("/category/{title}", name="categoryPostList")
     */
    public function categoryPostListAction($title)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('BlogBundle:Post');
        $posts = $repository->getLastArticlesByCategory($title);
         $postPathArray = array();
        
        foreach ($posts as $post){
            $postPathArray[$post->getId()] = $this->slugify($post->getTitle());
        }
        
        return $this->render('BlogBundle:Default:categoryPostList.html.twig',
            [
                'posts' => $posts,
                'postPathArray' => $postPathArray
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
     */
    public function showPostAction($title)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repositoryPost = $em->getRepository('BlogBundle:Post');
        $post = $repositoryPost->getPostByTitle($title);
        $repositoryComment = $em->getRepository('BlogBundle:Comment');
        $comments = $repositoryComment->findByPost(array('post' => $post));
        
        return $this->render('BlogBundle:Default:showPost.html.twig',
            [
                'post' => $post,
                'comments' => $comments
            ]);
    }
    
    /**
     * @Route("/comment/post", name="postComment")
     */
    public function postCommentAction($title)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repositoryPost = $em->getRepository('BlogBundle:Comment');
        
        return $this->render('BlogBundle:Default:showPost.html.twig',
            [

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
