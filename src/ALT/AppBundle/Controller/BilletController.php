<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 08/02/2017
 * Time: 17:51
 */

namespace ALT\AppBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ALT\AppBundle\Entity\Billet;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BilletController extends Controller
{
    

    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billet
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lectureAction(Billet $billet)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        // On récupère le répository de l'entité Commentaire, on lui appelle la méthode "findBy"
        // pour récupérer les commentaires depuis notre base de données, en lui passant en  paramètre "id" de ce billet
        $listeCommentaires = $em
            ->getRepository('ALTAppBundle:Commentaire')
            ->findBy(array('billet' => $billet));
        // On affiche la page qui va afficher la lecture , on fait passer les paramètres dans la vue
        return $this->render('ALTAppBundle:Billet:lecture.html.twig', array(
            'billet' => $billet,
            'listeCommentaires' => $listeCommentaires
        ));
    }

}