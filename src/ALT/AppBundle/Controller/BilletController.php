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

    /**
     * @param $limit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        // On récupère le répository de l'entité Billet, on lui appelle la méthode "findBy"
        // pour récupérer des billets depuis notre base de données, triés par "id" en ordre descendant avec une limite
        $listeBillets = $em->getRepository('ALTAppBundle:Billet')->findBy(array(),array("id"=>"desc"),$limit);

        // On affiche la page qui va afficher le menu , on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Billet:menu.html.twig',array(
            'listeBillets' => $listeBillets
        ));
    }

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
            ->findBy(array('billet' => $billet))
        ;
        // On affiche la page qui va afficher la lecture , on fait passer les paramètres dans la vue
        return $this->render('ALTAppBundle:Billet:lecture.html.twig', array(
            'billet'           => $billet,
            'listeCommentaires' => $listeCommentaires
        ));
    }

    /**
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function monBlogAction($page)
    {
        // On ne sait pas combien de pages il y a, mais une page doit être supérieure ou égale à 1
        if ($page < 1) {
            // On déclenche une exception NotFoundHttpException, cela va afficher
            // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        // On récupère le répository de l'entité Billet, on lui appelle la méthode "findBy"
        // pour récupérer lesbillets depuis notre base de données, triés par "id" en ordre descendant
        // avec en paramètre une limite de 5 billets
        $listeBillets = $em->getRepository('ALTAppBundle:Billet')->findBy(array(),array("id"=>"desc"),5);

        // On affiche la page qui va afficher monblog , on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Billet:monblog.html.twig', array(
            'billets' => $listeBillets
        ));
    }
}