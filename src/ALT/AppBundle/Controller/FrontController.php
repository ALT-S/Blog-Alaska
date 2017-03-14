<?php

namespace ALT\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accueilAction($page)
    {
        $billetsParPage = 4; // Fixe le nombre de billets affichés par page

        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $repository = $em->getRepository('ALTAppBundle:Billet');

        $resultat = $repository->paginerBillets($page, $billetsParPage);

        $listeBillets = $resultat['billets'];
        $pagesTotal = $resultat['pagesTotal'];

        // On affiche la page qui va afficher le front Accueil, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Front:accueil.html.twig', array(
            'billets' => $listeBillets,
            'page' => $page,
            'pagesTotal' => $pagesTotal
        ));
    }

    /**
     * @param $limit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        // On récupère le répository de l'entité Billet, on lui appelle la méthode "findBy"
        // pour récupérer des commentaires depuis notre base de données, triés par "id" en ordre descendant avec une limite
        $listeCommentaires = $em->getRepository('ALTAppBundle:Commentaire')->findBy(array(), array("id" => "desc"), $limit);

        // On affiche la page qui va afficher le menu , on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Front:menu.html.twig', array(
            'listeCommentaires' => $listeCommentaires,
        ));
    }
}
