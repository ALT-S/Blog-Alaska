<?php

namespace ALT\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontController extends Controller
{
    /**
     * Fixe le nombre de billets affichés par page
     * 
     * On récupère le manager de Doctrine
     * 
     * On récupère le répo de la classe Billet depuis le manager de doctrine ( DB )
     * 
     * On demande au repo de récupérer paginerBillets via une page et un billetsParPage si ils existent
     * 
     * On affiche la vue page d'accueil
     * 
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function accueilAction($page)
    {
        $billetsParPage = 4;

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ALTAppBundle:Billet');

        $resultat = $repository->paginerBillets($page, $billetsParPage);

        $listeBillets = $resultat['billets'];
        $pagesTotal = $resultat['pagesTotal'];
        
        return $this->render('ALTAppBundle:Front:accueil.html.twig', array(
            'billets' => $listeBillets,
            'page' => $page,
            'pagesTotal' => $pagesTotal
        ));
    }

    /**
     * On récupère le manager de Doctrine
     * 
     * On récupère le répository de l'entité Billet, on lui appelle la méthode "findBy"
     * pour récupérer des commentaires depuis notre base de données, triés par "id" en ordre descendant avec une limite
     * 
     * On affiche la vue menu
     * 
     * @param $limit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        
        $listeCommentaires = $em->getRepository('ALTAppBundle:Commentaire')->findBy(array(), array("id" => "desc"), $limit);
        
        return $this->render('ALTAppBundle:Front:menu.html.twig', array(
            'listeCommentaires' => $listeCommentaires,
        ));
    }
}
