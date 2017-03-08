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
use ALT\AppBundle\Entity\Contact;
use ALT\AppBundle\Form\BilletType;
use ALT\AppBundle\Form\CommentaireType;
use ALT\AppBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données

        $qb = $em->getRepository('ALTAppBundle:Billet')->createQueryBuilder('b'); // Création du querybuilder pour l'entité "Billet"
        $qb->andWhere($qb->expr()->in('b.publier', 0))
            ->select('COUNT(b.id)'); // On veut récupérer le  nombre de billets via la fonction COUNT()

        $nbBilletsDepublies = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbBillets

        $qb = $em->getRepository('ALTAppBundle:Commentaire')->createQueryBuilder('c'); // Création du querybuilder pour l'entité "Commentaire"
        $qb->select('COUNT(c.id)'); // On veut récupérer le nombre de commentaires via la fonction COUNT()

        $nbCommentaires = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbCommentaires

        $qb = $em->getRepository('ALTAppBundle:Commentaire')->createQueryBuilder('c'); // Création du querybuilder pour l'entité "Commentaire"
        $qb->andWhere($qb->expr()->in('c.signale', 1))// ->andWhere('c.signale = 1')
        ->select('COUNT(c.id)'); // On veut récupérer le nombre de commentaires via la fonction COUNT()

        $nbContactsSignales = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbCommentaires


        $qb = $em->getRepository('ALTAppBundle:Contact')->createQueryBuilder('c'); // Création du querybuilder pour l'entité "Contact"
        $qb->select('COUNT(c.id)'); // On veut récupérer le  nombre de contacts via la fonction COUNT()

        $nbContacts = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbContacts

        $qb = $em->getRepository('ALTAppBundle:Contact')->createQueryBuilder('cr'); // Création du querybuilder pour l'entité "Contact"
        $qb->andWhere($qb->expr()->isNull('cr.dateReponse'))
            ->select('COUNT(cr.id)'); // On veut récupérer le  nombre de contacts via la fonction COUNT()

        $nbContactsAttenteReponse = $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbContacts


        // On affiche la page qui va afficher l'accueil, on fait passer les paramètres dans la vue
        return $this->render('@ALTApp/Admin/accueil.html.twig', array(
            'nbBillets' => $nbBillets,
            'nbBilletsDepublies' => $nbBilletsDepublies,
            'nbCommentaires' => $nbCommentaires,
            'nbContactsSignales' => $nbContactsSignales,
            'nbContacts' => $nbContacts,
            'nbContactsAttenteReponse' => $nbContactsAttenteReponse
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

        //Externalisation du formulaire dans Form Billet
        $formAjouter = $this->get('form.factory')->create(BilletType::class, $billet);

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
        //Externalisation du formulaire dans Form Billet
        $form = $this->get('form.factory')->create(BilletType::class, $billet);

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
    public function listeBilletsAction(Request $request)
    {

        $filtre = $request->query->get('filtre');//On récupère le paramètre dans l'URL qui s'appel filtre

        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $qb = $em->getRepository('ALTAppBundle:Billet')->createQueryBuilder('b');// Création du querybuilder pour l'entité "Billet"
        $qb->orderBy('b.id', 'desc');// pour récupérer des billets depuis notre base de données, triés par "id" en ordre descendant.

        //Si on  récupère le paramètre filtre et que celui-ci est bien depublie, alors on récupère les billets qui sont dépubliés
        if (isset($filtre) && $filtre == 'depublie') {
            $qb->andWhere('b.publier = 0');
        }

        $billets = $qb->getQuery()->getResult();

        // On affiche la page qui va afficher la liste des billets, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:liste_billets.html.twig', array(
            'billets' => $billets
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeContactsAction(Request $request)
    {
        $filtre = $request->query->get('filtre');//On récupère le paramètre dans l'URL qui s'appel filtre

        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $qb = $em->getRepository('ALTAppBundle:Contact')->createQueryBuilder('c');// Création du querybuilder pour l'entité "BContact"
        $qb->orderBy('c.id', 'desc');// pour récupérer des contacts depuis notre base de données, triés par "id" en ordre descendant.

        //Si on  récupère le paramètre filtre et que celui-ci est bien attente, alors on récupère les contact qui sont en attente de réponses
        if (isset($filtre) && $filtre == 'attente') {
            $qb->andWhere('c.dateReponse IS NULL');
        }

        $contacts = $qb->getQuery()->getResult();


        // On affiche la page qui va afficher la liste des contacts, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:liste_contacts.html.twig', array(
            'contacts' => $contacts,
        ));
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeCommentairesAction(Request $request)
    {
        $filtre = $request->query->get('filtre');

        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $qb = $em->getRepository('ALTAppBundle:Commentaire')->createQueryBuilder('c');// Création du querybuilder pour l'entité "Commentaire"
        $qb->orderBy('c.id', 'desc');// pour récupérer des commentaires depuis notre base de données, triés par "id" en ordre descendant.

        //Si on  récupère le paramètre filtre et que celui-ci est bien signaler, alors on récupère les commentaire qui sont signalés
        if (isset($filtre) && $filtre == 'signaler') {
            $qb->andWhere('c.signale = 1');
        }

        $commentaires = $qb->getQuery()->getResult();

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
        $commentaires = $em->getRepository('ALTAppBundle:Commentaire')->findBy(array('billet' => $billet), array("id" => "desc"));

        // On affiche la page qui va afficher la liste des commentaires par "id", on fait passer les paramètres dans la vue
        return $this->render('ALTAppBundle:Admin:liste_commentaires_par_id.html.twig', array(
            'commentaires' => $commentaires,
            'billet' => $billet
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
        $em->remove($commentaire);//On supprime l'entité $commentaire
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Le commentaire a bien été supprimé !');

        // On redirige vers la page qui va afficher la liste des commentaires
        return $this->redirectToRoute('alt_app_admin_liste_commentaires');
    }

    /**
     * @param Commentaire $commentaireValider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validerCommentaireAction(Commentaire $commentaireValider)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $commentaireValider->setSignale(false);
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Le commentaire a bien été validé !');

        // On redirige vers la page qui va afficher la liste des commentaires
        return $this->redirectToRoute('alt_app_admin_liste_commentaires');
    }

    /**
     * Récupération de billets via ParamConverter
     *
     * @param Billet $billetPublie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publierBilletAction(Billet $billetPublie)
    {

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
    public function depublierBilletAction(Billet $billetPublie)
    {

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
        //Externalisation du formulaire dans Form Commentaire
        $form = $this->get('form.factory')->create(CommentaireType::class, $commentaire);

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $commentaire contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $commentaire dans la base de données
                $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
                $commentaire->setSignale(false);
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

    public function reponseContactAction(Contact $contact, Request $request)
    {
        //Externalisation du formulaire dans Form Contact
        $form = $this->get('form.factory')->create(ContactType::class, $contact);
        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $contact contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                $contact->setDateReponse(new \DateTime());

                // On enregistre notre objet $contact  dans la base de données
                $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
                $em->persist($contact);// puis on « persiste » l'entité, garde cette entité en mémoire
                $em->flush();// Et on déclenche l'enregistrement

                // Création de l'e-mail avec SwiftMailer
                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')//Message en HTML
                    ->setSubject("Réponse à : " . $contact->getSujet())//Email et le titre du mail devient le sujet de mon objet contact
                    ->setFrom($this->getParameter('mailer_user'))// Email de l'expéditeur - nous
                    ->setTo($contact->getEmail())// destinataire du mail
                    ->setBody($this->renderView('@ALTApp/Admin/mail_reponse_contact.html.twig', array(
                        'contact' => $contact
                    ))); // contenu réponse du mail + contenu constact

                //Envoi mail
                $this->get('mailer')->send($message);
            }

            // Création du « flashBag » qui contient les messages flash
            $this->addFlash('notice', 'Le réponse a bien été envoyée !');

            // On redirige vers la page qui va afficher la liste des contacts
            return $this->redirectToRoute('alt_app_admin_liste_contacts');
        }

        return $this->render('ALTAppBundle:Admin:reponseContact.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));

    }

    public function lectureContactReponseAction(Contact $contact)
    {
        // On affiche la page qui va afficher la lecture de billet, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:lecture_contact_reponse.html.twig', array(
            'contact' => $contact,
        ));
    }
}
