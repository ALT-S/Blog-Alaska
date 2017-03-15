<?php

namespace ALT\AppBundle\Controller;

use ALT\AppBundle\Entity\Billet;
use ALT\AppBundle\Entity\Commentaire;
use ALT\AppBundle\Form\CommentaireType;
use ALT\AppBundle\Form\ReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentairesController extends Controller
{
    /**
     * On récupère le manager de Doctrine
     * 
     * Création de l'entité - objet Commentaire
     * 
     * On lie le billet au commentaire
     * 
     * Création d'un formulaire du type commentaireType
     * 
     * Si la requête est en POST alors :
     * On attache les données du formulaire dans l'objet de formulaire et rempli l'objet $commentaire de ces données
     * 
     * Si le formulaire a été soumis et qu'il est valide alors :
     * On demande au manager de garder en mémoire l'objet $billet
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     * 
     * On redirige l'utilisateur vers une autre route
     * 
     * Sinon, on affiche la vue contenant le formulaire et ses possibles erreurs si il y en a.
     * 
     * @param Billet $billet
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterCommentaireAction(Billet $billet, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $commentaire = new Commentaire();

        $commentaire->setBillet($billet);
        
        $form = $this->get('form.factory')->create(CommentaireType::class, $commentaire);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commentaire);
            $em->flush();
            
            $this->addFlash('notice', 'Le commentaire a bien été enregistré !');
            
            return $this->redirectToRoute('alt_app_lecture', array('id' => $billet->getId()));
        }
        
        return $this->render('ALTAppBundle:Billet:ajout_commentaire.html.twig', array(
            'billet'=> $billet,
            'form' => $form->createView()
        ));
    }

    /**
     * On récupère le manager de Doctrine
     * 
     * Création de l'entité - objet Commentaire
     * 
     * On lie le commentaire au commentaireParent
     * 
     * Création d'un formulaire du type reponseType
     * 
     * Si la requête est en POST alors :
     * On attache les données du formulaire dans l'objet de formulaire et rempli l'objet $commentaire de ces données
     * 
     * Si le formulaire a été soumis et qu'il est valide alors :
     * On récupère le manager de Doctrine
     * On demande au manager de garder en mémoire l'objet $commentaire
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     * On redirige l'utilisateur vers une autre route
     *
     * Sinon, on affiche la vue contenant le formulaire et ses possibles erreurs si il y en a.
     * 
     * @param Commentaire $commentaireParent
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function repondreCommentaireAction(Commentaire $commentaireParent, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $commentaire = new Commentaire();

        $commentaire->setParent($commentaireParent);
        
        $form = $this->get('form.factory')->create(ReponseType::class, $commentaire);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commentaire);
            $em->flush();
            
            $this->addFlash('notice', 'Le commentaire a bien été enregistré !');
            
            return $this->redirectToRoute('alt_app_lecture', array("id" => $commentaireParent->getBillet()->getId()));
        }
        
        return $this->render('ALTAppBundle:Billet:formulaire_reponse.html.twig', array(
            'commentaireParent'=>$commentaireParent,
            'form' => $form->createView()
        ));
    }

    /**
     * On récupère le manager de Doctrine
     * On dit que le signale du commentaire est vrai
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     * 
     * On redirige l'utilisateur vers une autre route
     * 
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function signalerAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire->setSignale(true);
        $em->flush();
        
        $this->addFlash('notice', 'Le commentaire a bien été signalé !');

        return $this->redirectToRoute('alt_app_lecture', array(
            "id" => $commentaire->getBillet()->getId()));
    }
}