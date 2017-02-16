<?php

namespace ALT\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function accueilAction()
    {
        $em = $this->getDoctrine()->getManager();
        $billets = $em->getRepository('ALTAppBundle:Billet')->findBy(array("publier" => 1),array("id"=>"desc"),5);

        return $this->render('ALTAppBundle:Front:accueil.html.twig', array(
            'billets' => $billets
        ));
    }
}
