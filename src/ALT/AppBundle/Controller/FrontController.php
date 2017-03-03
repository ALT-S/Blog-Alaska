<?php

namespace ALT\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accueilAction()
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        // On récupère le répository de l'entité Billet, on lui appelle la méthode "findBy"
        // pour récupérer les billets depuis notre base de données, avec pour contrainte les billets publiés
        // triés par "id" en ordre descendant
        // avec en paramètre une limite de 5 billets
        $billets = $em->getRepository('ALTAppBundle:Billet')->findBy(array("publier" => 1), array("id" => "desc"), 5);

        // On affiche la page qui va afficher le front Accueil, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Front:accueil.html.twig', array(
            'billets' => $billets
        ));
    }
}
