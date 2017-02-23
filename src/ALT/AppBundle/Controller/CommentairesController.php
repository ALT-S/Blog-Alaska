<?php

namespace ALT\AppBundle\Controller;

use ALT\AppBundle\Entity\Billet;
use ALT\AppBundle\Entity\Commentaire;
use ALT\AppBundle\Form\CommentaireType;
use ALT\AppBundle\Form\DemandeContactType;
use ALT\AppBundle\Form\SignalerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentairesController extends Controller
{
    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billet
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterCommentaireAction(Billet $billet, Request $request)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        $commentaire = new Commentaire();// Création de l'entité - objet Commentaire

        $commentaire->setBillet($billet); // On lie le billet au commentaire
        $commentaire->setDate(new \Datetime()); // On préremplit date du jour

        //Externalisation du formulaire dans Form Commentaire
        $form = $this->get('form.factory')->create(CommentaireType::class, $commentaire);

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $billet contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $billet dans la base de données
                // On a déjà récupèré l'EntityManager pour dialoguer avec la base de données
                $em->persist($commentaire);// puis on « persiste » l'entité, garde cette entité en mémoire
                $em->flush();// Et on déclenche l'enregistrement

                // Création du « flashBag » qui contient les messages flash
                $this->addFlash('notice', 'Le commentaire a bien été enregistré !');

                // On redirige vers la page qui va afficher la lecture du billet on fait passer le paramètre dans la vue
                return $this->redirectToRoute('alt_app_lecture', array('id' => $billet->getId()));
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        // Donc on affiche la page qui va afficher ajouter un commentaire, on fait passer le paramètre form dans la vue
        return $this->render('ALTAppBundle:Billet:ajout_commentaire.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function signalerAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $commentaire ->setSignale(true);
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Le commentaire a bien été signalé !');

        return $this->redirectToRoute('alt_app_lecture', array(
            "id"=>$commentaire->getBillet()->getId()));
    }
}