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
        $em = $this->getDoctrine()->getManager();
        
        $repository = $em->getRepository('ALTAppBundle:Billet');
        $nbBillets = $repository->countNbBillets(); // On récupère le résultat du comptage dans $nbBillets
        $nbBilletsDepublies = $repository->countNbBilletDepublie();; // On récupère le résultat du comptage dans $nbBilletsDepublies
        $nbBilletsPublies = $repository->countNbBilletPublie();; // On récupère le résultat du comptage dans $nbBilletsPublies

        $repository = $em->getRepository('ALTAppBundle:Commentaire');
        $nbCommentaires = $repository->countNbCommentaires(); // On récupère le résultat du comptage dans $nbCommentaires
        $nbCommentairesSignales = $repository->countNbCommentairesSignales();// On récupère le résultat du comptage dans $nbContactsSignales

        $repository = $em->getRepository('ALTAppBundle:Contact');
        $nbContacts = $repository->countNbContacts(); // On récupère le résultat du comptage dans $nbContacts
        $nbContactsAttenteReponse = $repository->countNbContactsAttenteReponse(); // On récupère le résultat du comptage dans $nbContacts

        // On affiche la page qui va afficher l'accueil, on fait passer les paramètres dans la vue
        return $this->render('@ALTApp/Admin/accueil.html.twig', array(
            'nbBillets' => $nbBillets,
            'nbBilletsPublies' => $nbBilletsPublies,
            'nbBilletsDepublies' => $nbBilletsDepublies,
            'nbCommentaires' => $nbCommentaires,
            'nbCommentairesSignales' => $nbCommentairesSignales,
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

        $repository = $this->getDoctrine()->getManager()->getRepository('ALTAppBundle:Billet');
        $billets = $repository->listeBillets($filtre);

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

        $repository = $this->getDoctrine()->getManager()->getRepository('ALTAppBundle:Contact');
        $contacts  = $repository->listeContacts($filtre);

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

        $em = $this->getDoctrine()->getManager();

        $billet = null;
        if (isset($filtre) && $filtre == 'billet') {
            $billet = $em->find('ALTAppBundle:Billet', $request->query->get('id'));
        }

        $repository = $em->getRepository('ALTAppBundle:Commentaire');//On récupère le manager pour dialoguer avec la base de données
        $commentaires = $repository->listeCommentaires($filtre, $billet);

        $parametres = ['commentaires' => $commentaires]; // On prépare les paramètres pour la vue
        if (isset($billet)) { // Si le billet "existe" (c'est à dire qu'on a un filtre "billet" + son ID
            $parametres['billet'] = $billet; // on ajoute ce fameux billet aux paramètres de la vue.
        }

        // On affiche la page qui va afficher la liste des commentaires, on fait passer le paramètre dans la vue
        return $this->render('ALTAppBundle:Admin:liste_commentaires.html.twig', $parametres);
    }

    public function supprimerContactAction(Contact $contact)
    {
        $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
        $em->remove($contact);//On supprime l'entité $billet
        $em->flush();// Et on déclenche l'enregistrement

        // Création du « flashBag » qui contient les messages flash
        $this->addFlash('notice', 'Le ccontact a bien été supprimé !');

        // On redirige vers la page qui va afficher la liste des commentaires
        return $this->redirectToRoute('alt_app_admin_liste_contacts');
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
