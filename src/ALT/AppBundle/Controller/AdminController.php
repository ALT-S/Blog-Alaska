<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 13/02/2017
 * Time: 22:18
 */

namespace ALT\AppBundle\Controller;


use ALT\AppBundle\Entity\Billet;
use ALT\AppBundle\Entity\Commentaire;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{

    public function accueilAction()
    {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('ALTAppBundle:Billet')->createQueryBuilder('b');
        $qb->select('COUNT(b.id)');

        $nbBillets = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->getRepository('ALTAppBundle:Commentaire')->createQueryBuilder('c');
        $qb->select('COUNT(c.id)');

        $nbCommentaires = $qb->getQuery()->getSingleScalarResult();

        //$billets = $em->getRepository('ALTAppBundle:Billet')->findBy(array(),array("id"=>"desc"));

        return $this->render('@ALTApp/Admin/accueil.html.twig', array(
            'nbBillets' => $nbBillets,
            'nbCommentaires' => $nbCommentaires

        ));

    }

    public function lectureAction(Billet $billet)
    {
        return $this->render('ALTAppBundle:Admin:admin_lecture.html.twig', array(
            'billet'           => $billet
        ));

    }

    public function ajouterAction(Request $request)
    {
        // Création de l'entité - objet Billet
        $billet = new Billet();
        // préremplit date du jour
        $billet->setDate(new \Datetime());
        //Création du formulaire "FormBuilder" par le service form factory
        $formAjouter= $this->get('form.factory')->createBuilder(FormType::class, $billet)
            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            ->add('date',      DateType::class)
            ->add('titre',     TextType::class)
            ->add('contenu',   TextareaType::class)
           // ->add('auteur',    TextType::class)
            ->add('publier', CheckboxType::class, array('required' => false))
            ->add('enregistrer',      SubmitType::class)
            ->getForm()
        ;
        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $billet contient les valeurs entrées dans le formulaire par le visiteur
            $formAjouter->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($formAjouter->isValid()) {
                // On enregistre notre objet $billet dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($billet);
                $em->flush();

                $this->addFlash('notice', 'Le billet a bien été enregistré !');

                // On redirige vers la page de visualisation du billet crée
                return $this->redirectToRoute('alt_app_admin_liste_billets');
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('ALTAppBundle:Admin:ajouter.html.twig', array(
            'form' => $formAjouter->createView(),
        ));
    }


    public function modifierAction(Billet $billet, Request $request)
    {
        // Récupération d'un billet déjà existant par ParamConverter

        // Et on construit le formBuilder avec l' instance du billet
        $form= $this->get('form.factory')->createBuilder(FormType::class, $billet)
            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            ->add('date',      DateType::class)
            ->add('titre',     TextType::class)
            ->add('contenu',   TextareaType::class)
            ->add('publier', CheckboxType::class, array('required' => false))
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
                $em = $this->getDoctrine()->getManager();
                $em->persist($billet);
                $em->flush();
            }

            $this->addFlash('notice', 'Le billet a bien été modifié !');

            // On redirige vers la page de visualisation du billet crée
            return $this->redirectToRoute('alt_app_admin_liste_billets');
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('ALTAppBundle:Admin:modifier.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function supprimerAction(Billet $billet)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($billet);
        $em->flush();

        // ajoute un message flash pour dire que c'est bon
        $this->addFlash('notice', 'Billet a bien été supprimé !');

        // redirection vers la page liste
        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    public function listeBilletsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $billets = $em->getRepository('ALTAppBundle:Billet')->findBy(array(),array("id"=>"desc"));

        return $this->render('ALTAppBundle:Admin:liste_billets.html.twig', array(
            'billets' => $billets
        ));
    }

    public function listeContactsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('ALTAppBundle:Contact')->findBy(array(),array("id"=>"desc"));


        return $this->render('ALTAppBundle:Admin:liste_contacts.html.twig', array(
            'contacts' => $contacts,

        ));
    }

    public function listeCommentairesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $commentaires = $em->getRepository('ALTAppBundle:Commentaire')->findBy(array(),array("id"=>"desc"));


        return $this->render('ALTAppBundle:Admin:liste_commentaires.html.twig', array(
            'commentaires' => $commentaires,

        ));
    }

    public function listeCommentairesParIdAction(Billet $billet)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaires = $em->getRepository('ALTAppBundle:Commentaire')->findBy(array('billet' => $billet),array("id"=>"desc"));

        return $this->render('ALTAppBundle:Admin:liste_commentaires_par_id.html.twig', array(
            'commentaires' => $commentaires,
            'billet'           => $billet
        ));
    }

    public function supprimerCommentaireAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();

        // ajoute un message flash pour dire que c'est bon
        $this->addFlash('notice', 'Le commentaire a bien été supprimé !');

        // redirection vers la page liste
        return $this->redirectToRoute('alt_app_admin_liste_commentaires');
    }

    public function publierBilletAction(Billet $billetPublie){

        $em = $this->getDoctrine()->getManager();
        $billetPublie->setPublier(true);
        $em->flush();

        // ajoute un message flash pour dire que c'est bon
        $this->addFlash('notice', 'Le billet a bien été publié !');

        // redirection vers la page liste
        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    public function depublierBilletAction(Billet $billetPublie){

        $em = $this->getDoctrine()->getManager();
        $billetPublie->setPublier(false);
        $em->flush();

        // ajoute un message flash pour dire que c'est bon
        $this->addFlash('notice', 'Le billet a bien été dépublié !');

        // redirection vers la page liste
        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    public function modifierCommentaireAction(Commentaire $commentaire, Request $request)
    {
        // Et on construit le formBuilder avec l' instance du commentaire
        $form= $this->get('form.factory')->createBuilder(FormType::class, $commentaire)
            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            ->add('date',      DateType::class)
            ->add('contenu',   TextareaType::class)
            ->add('auteur', TextType::class)
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
                $em = $this->getDoctrine()->getManager();
                $em->persist($commentaire);
                $em->flush();
            }

            $this->addFlash('notice', 'Le commentaire a bien été modifié !');

            // On redirige vers la page de visualisation du billet crée
            return $this->redirectToRoute('alt_app_admin_liste_commentaires');
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('ALTAppBundle:Admin:modifier.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}