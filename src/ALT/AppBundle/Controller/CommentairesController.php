<?php

namespace ALT\AppBundle\Controller;

use ALT\AppBundle\Entity\Billet;
use ALT\AppBundle\Entity\Commentaire;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class CommentairesController extends Controller
{
    public function ajouter_commentaireAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        // par le repository, on récupère l'entité correspondante au billet avec l'id : $id
        // ($billet est une instance de JF\BlogBundle\Entity\Billet)
        $billet = $em->getRepository('ALTAppBundle:Billet')->find($id);


        // Création de l'entité - objet Billet
        $commentaire = new Commentaire();

        $commentaire->setBillet($billet); // lier billet au commentaire
        $commentaire->setDate(new \Datetime()); // préremplit date du jour

        //Création du formulaire "FormBuilder" par le service form factory
        $form= $this->get('form.factory')->createBuilder(FormType::class, $commentaire)
            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            ->add('date',      DateType::class)
            ->add('auteur',    TextType::class)
            ->add('titre',     TextType::class)
            ->add('contenu',   TextareaType::class)
            ->add('enregistrer',      SubmitType::class)
            ->getForm()
        ;
        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $billet contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $billet dans la base de données
                // On récupère l'EntityManager

                $em->persist($commentaire);
                // On déclenche l'enregistrement
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Le commentaire a bien été enregistré !');

                // On redirige vers la page de visualisation du billet crée
                return $this->redirectToRoute('alt_app_lecture', array('id' => $billet->getId()));
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('ALTAppBundle:Billet:ajout_commentaire.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    public function modifier_commentaireAction($id)
    {

    }
}