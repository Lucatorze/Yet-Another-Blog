<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('BlogBundle:Post');
        $posts = $repository->findAll();
        
        return $this->render('BlogBundle:Default:index.html.twig',
            ['posts' => $posts]);
    }
    
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
}
