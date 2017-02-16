<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 08/02/2017
 * Time: 17:51
 */

namespace ALT\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ALT\AppBundle\Entity\Billet;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BilletController extends Controller
{


    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $listeBillets = $em->getRepository('ALTAppBundle:Billet')->findBy(array(),array("id"=>"desc"),$limit);
        return $this->render('ALTAppBundle:Billet:menu.html.twig',array(
            'listeBillets' => $listeBillets
        ));

    }

    public function lectureAction($id)
    {
        // on appel le manager
        $em = $this->getDoctrine()->getManager();
        // par le repository, on récupère l'entité correspondante au billet avec l'id : $id
        // ($billet est une instance de JF\BlogBundle\Entity\Billet)
        $billet = $em->getRepository('ALTAppBundle:Billet')->find($id);
        
        //si le billet est nul ou n'existe pas, on affiche un message
        if (null === $billet) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On récupère la liste des commentaires de ce billet
        $listeCommentaires = $em
            ->getRepository('ALTAppBundle:Commentaire')
            ->findBy(array('billet' => $billet))
        ;
    // on retourne la vue avec l'objet $billet
        return $this->render('ALTAppBundle:Billet:lecture.html.twig', array(
            'billet'           => $billet,
            'listeCommentaires' => $listeCommentaires
        ));

    }

    

    public function monblogAction($page)
    {

        // On ne sait pas combien de pages il y a
        // Mais on sait qu'une page doit être supérieure ou égale à 1
        if ($page < 1) {
            // On déclenche une exception NotFoundHttpException, cela va afficher
            // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        $em = $this->getDoctrine()->getManager();
        $listeBillets = $em->getRepository('ALTAppBundle:Billet')->findBy(array(),array("id"=>"desc"),5);

        return $this->render('ALTAppBundle:Billet:monblog.html.twig', array(
            'billets' => $listeBillets
        ));
    }
    
}