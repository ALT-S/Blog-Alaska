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

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accueilAction()
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        $qb = $em->getRepository('ALTAppBundle:Billet')->createQueryBuilder('b'); // Création du querybuilder pour l'entité "Billet"
        $qb->select('COUNT(b.id)'); // On veut récupérer le  nombre de billets via la fonction COUNT()

        $nbBillets = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbBillets

        $qb = $em->getRepository('ALTAppBundle:Commentaire')->createQueryBuilder('c'); // Création du querybuilder pour l'entité "Commentaire"
        $qb->select('COUNT(c.id)'); // On veut récupérer le nombre de commentaires via la fonction COUNT()

        $nbCommentaires = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbCommentaires

        // On affiche la page qui va afficher l'accueil, on fait passer les paramètres dans la vue
        return $this->render('@ALTApp/Admin/accueil.html.twig', array(
            'nbBillets' => $nbBillets,
            'nbCommentaires' => $nbCommentaires
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
        // On affiche la page qui va afficher la lecture de billet, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:admin_lecture.html.twig', array(
            'billet' => $billet
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterAction(Request $request)
    {
        $billet = new Billet();// Création de l'entité - objet Billet
        $billet->setDate(new \Datetime());// On préremplit date du jour
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
                $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
                $em->persist($billet);// puis on « persiste » l'entité, garde cette entité en mémoire
                $em->flush();// Et on déclenche l'enregistrement

                // Création du « flashBag » qui contient les messages flash
                $this->addFlash('notice', 'Le billet a bien été enregistré !');

                // On redirige vers la page qui va afficher la liste des billets
                return $this->redirectToRoute('alt_app_admin_liste_billets');
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        // Donc on affiche la page qui va afficher la page ajouter, on fait passer le paramètre formAjouter  dans la vue
        return $this->render('ALTAppBundle:Admin:ajouter.html.twig', array(
            'form' => $formAjouter->createView(),
        ));
    }

    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billet
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifierAction(Billet $billet, Request $request)
    {
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
                $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
                $em->persist($billet);// puis on « persiste » l'entité, garde cette entité en mémoire
                $em->flush();// Et on déclenche l'enregistrement
            }

            // Création du « flashBag » qui contient les messages flash
            $this->addFlash('notice', 'Le billet a bien été modifié !');

            // On redirige vers la page qui va afficher la liste des billets
            return $this->redirectToRoute('alt_app_admin_liste_billets');
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        // Donc on affiche la page qui va afficher la page modifier, on fait passer le paramètre form dans la vue
        return $this->render('ALTAppBundle:Admin:modifier.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billet
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerAction(Billet $billet)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $em->remove($billet);//On supprime l'entité $billet
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Billet a bien été supprimé !');

        // On redirige vers la page qui va afficher la liste des billets
        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeBilletsAction()
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        // On récupère le répository de l'entité Billet, on lui appelle la méthode "findBy"
        // pour récupérer des billets depuis notre base de données, triés par "id" en ordre descendant.
        $billets = $em->getRepository('ALTAppBundle:Billet')->findBy(array(),array("id"=>"desc"));

        // On affiche la page qui va afficher la liste des billets, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:liste_billets.html.twig', array(
            'billets' => $billets
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeContactsAction()
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        // On récupère le répository de l'entité Contact, on lui appelle la méthode "findBy"
        // pour récupérer des contacts depuis notre base de données, triés par "id" en ordre descendant.
        $contacts = $em->getRepository('ALTAppBundle:Contact')->findBy(array(),array("id"=>"desc"));

        // On affiche la page qui va afficher la liste des contacts, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:liste_contacts.html.twig', array(
            'contacts' => $contacts,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeCommentairesAction()
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        // On récupère le répository de l'entité Commentaire, on lui appelle la méthode "findBy"
        // pour récupérer des commentaires depuis notre base de données, triés par "id" en ordre descendant.
        $commentaires = $em->getRepository('ALTAppBundle:Commentaire')->findBy(array(),array("id"=>"desc"));

        // On affiche la page qui va afficher la liste des commentaires, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:liste_commentaires.html.twig', array(
            'commentaires' => $commentaires,
        ));
    }

    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billet
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeCommentairesParIdAction(Billet $billet)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        // On récupère le répository de l'entité Commentaire, on lui appelle la méthode "findBy"
        // pour récupérer des commentaires depuis notre base de données, qui prend en paramètre "id" du billet et triés par "id" en ordre descendant.
        $commentaires = $em->getRepository('ALTAppBundle:Commentaire')->findBy(array('billet' => $billet),array("id"=>"desc"));

        // On affiche la page qui va afficher la liste des commentaires par "id", on fait passer les paramètres dans la vue
        return $this->render('ALTAppBundle:Admin:liste_commentaires_par_id.html.twig', array(
            'commentaires' => $commentaires,
            'billet'           => $billet
        ));
    }

    /**
     * Récupération de commentaires via ParamConverter
     *
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerCommentaireAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $em->remove($commentaire);//On supprime l'entité $billet
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Le commentaire a bien été supprimé !');

        // On redirige vers la page qui va afficher la liste des commentaires
        return $this->redirectToRoute('alt_app_admin_liste_commentaires');
    }

    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billetPublie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publierBilletAction(Billet $billetPublie){

        $em = $this->getDoctrine()->getManager(); // On récupère le manager pour dialoguer avec la base de données
        $billetPublie->setPublier(true); // On dit à l'objet "billetPublie" d'activer la publication de celui ci
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Le billet a bien été publié !');

        // On redirige vers la page qui va afficher la liste des billets
        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billetPublie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function depublierBilletAction(Billet $billetPublie){

        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $billetPublie->setPublier(false);// On dit à l'objet "billetPublie" de desactiver la publication de celui ci
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Le billet a bien été dépublié !');

        // On redirige vers la page qui va afficher la liste des billets
        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    /**
     * Récupération de commentaires via ParamConverter
     *
     * @param Commentaire $commentaire
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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
                $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
                $em->persist($commentaire);// puis on « persiste » l'entité, garde cette entité en mémoire
                $em->flush();// Et on déclenche l'enregistrement
            }

            // Création du « flashBag » qui contient les messages flash
            $this->addFlash('notice', 'Le commentaire a bien été modifié !');

            // On redirige vers la page qui va afficher la liste des commentaires
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