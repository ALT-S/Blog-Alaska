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
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        $qb = $em->getRepository('ALTAppBundle:Billet')->createQueryBuilder('b'); // Création du querybuilder pour l'entité "Billet"
        $qb->select('COUNT(b.id)'); // On veut récupérer le  nombre de billets via la fonction COUNT()

        $nbBillets = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbBillets

        $billetsParPage = 5; // Fixe le nombre de billets affichés par page

        $pagesTotal = (int) ceil($nbBillets / $billetsParPage); // Compte le nombre de pages totales (ceil() fait l'arrondie à l'entier supérieur)

        if ($page > $pagesTotal) {
            throw $this->createNotFoundException("Page maximum atteinte");
        }


        $offset = ($page - 1) * $billetsParPage; // Calcul du point de départ pour récupérer les enregistrements en base de données

        // On récupère le répository de l'entité Billet, on lui appelle la méthode "findBy"
        // pour récupérer les billets depuis notre base de données, triés par "id" en ordre descendant
        // avec en paramètre une limite de $billetsParPage en partant de $offset
        $listeBillets = $em->getRepository('ALTAppBundle:Billet')
            ->findBy(array(), array("id" => "desc"), $billetsParPage, $offset);


        // On affiche la page qui va afficher le front Accueil, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Front:accueil.html.twig', array(
            'billets' => $listeBillets,
            'page' => $page,
            'pagesTotal' => $pagesTotal
        ));
    }
}
