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
     * Affiche l'accueil
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * On récupère le résultat du comptage des repos dans:  $nbBillet, $nbBilletPublies,  $nbBilletDepublies
     *                                                      $nbCommentaires, $nbCommentairesSignales
     *                                                      $nbContacts et le $nbrContactsAttenteReponse
     */
    public function accueilAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('ALTAppBundle:Billet');
        $nbBillets = $repository->countNbBillets();
        $nbBilletsDepublies = $repository->countNbBilletDepublie();;
        $nbBilletsPublies = $repository->countNbBilletPublie();;

        $repository = $em->getRepository('ALTAppBundle:Commentaire');
        $nbCommentaires = $repository->countNbCommentaires();
        $nbCommentairesSignales = $repository->countNbCommentairesSignales();

        $repository = $em->getRepository('ALTAppBundle:Contact');
        $nbContacts = $repository->countNbContacts();
        $nbContactsAttenteReponse = $repository->countNbContactsAttenteReponse();

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
     * Affiche la page de lecture d'un billet
     *
     * @param Billet $billet
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lectureAction(Billet $billet)
    {
        return $this->render('ALTAppBundle:Admin:admin_lecture.html.twig', array(
            'billet' => $billet
        ));
    }

    /**
     * Création de l'entité - objet Billet
     * On préremplit date du jour
     *
     * Création d'un formulaire du type billetType
     *
     * Si la requête est en POST alors :
     * On attache les données du formulaire dans l'objet de formulaire et rempli l'objet $billet de ces données
     *
     * Si le formulaire a été soumis et qu'il est valide alors :
     * On récupère le manager de Doctrine
     * On demande au manager de garder en mémoire l'objet $billet
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     * On redirige l'utilisateur vers une autre route
     *
     * Sinon, on affiche la vue contenant le formulaire et ses possibles erreurs si il y en a.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterAction(Request $request)
    {
        $billet = new Billet();
        $billet->setDate(new \Datetime());

        $formAjouter = $this->get('form.factory')->create(BilletType::class, $billet);

        if ($request->isMethod('POST')) {
            $formAjouter->handleRequest($request);

            if ($formAjouter->isSubmitted() && $formAjouter->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($billet);
                $em->flush();

                $this->addFlash('notice', 'Le billet a bien été enregistré !');

                return $this->redirectToRoute('alt_app_admin_liste_billets');
            }
        }

        return $this->render('ALTAppBundle:Admin:ajouter.html.twig', array(
            'form' => $formAjouter->createView(),
        ));
    }

    /**
     * Création d'un formulaire du type billetType, prérempli de l'objet billet
     *
     * Si la requête est en POST alors :
     * On attache les données du formulaire dans l'objet de formulaire et rempli l'objet $billet de ces données
     *
     * Si le formulaire a été soumis et qu'il est valide alors :
     * On récupère le manager de Doctrine
     * On demande au manager de garder en mémoire l'objet $billet
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     * On redirige l'utilisateur vers une autre route
     *
     * Sinon, on affiche la vue contenant le formulaire et ses possibles erreurs si il y en a.
     *
     *
     * @param Billet $billet
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifierAction(Billet $billet, Request $request)
    {
        $form = $this->get('form.factory')->create(BilletType::class, $billet);

        if ($request->isMethod('POST')) {
           $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($billet);
                $em->flush();
            }

            $this->addFlash('notice', 'Le billet a bien été modifié !');

            return $this->redirectToRoute('alt_app_admin_liste_billets');
        }

        return $this->render('ALTAppBundle:Admin:modifier.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * On récupère le manager pour dialoguer avec la base de données
     * On supprime l'entité $billet
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     *
     * On redirige l'utilisateur vers la liste des billets
     *
     * @param Billet $billet
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerAction(Billet $billet)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($billet);
        $em->flush();

        $this->addFlash('notice', 'Billet a bien été supprimé !');

        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    /**
     * On récupère le paramètre dans l'URL qui s'appel filtre
     *
     * On récupère le répo de la classe Billet depuis le manager de doctrine ( DB )
     * on demande au repo de récupérer la liste des contacts via un filtre
     *
     * on affiche la vue liste des billets
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeBilletsAction(Request $request)
    {
        $filtre = $request->query->get('filtre');

        $repository = $this->getDoctrine()->getManager()->getRepository('ALTAppBundle:Billet');
        $billets = $repository->listeBillets($filtre);

        return $this->render('ALTAppBundle:Admin:liste_billets.html.twig', array(
            'billets' => $billets
        ));
    }

    /**
     * On récupère le paramètre dans l'URL qui s'appel filtre
     *
     * On récupère le répo de la classe Contact depuis le manager de doctrine ( DB )
     * on demande au repo de récupérer la liste des contacts via un filtre
     *
     * on affiche la vue liste des contacts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeContactsAction(Request $request)
    {
        $filtre = $request->query->get('filtre');

        $repository = $this->getDoctrine()->getManager()->getRepository('ALTAppBundle:Contact');
        $contacts = $repository->listeContacts($filtre);

        return $this->render('ALTAppBundle:Admin:liste_contacts.html.twig', array(
            'contacts' => $contacts,
        ));
    }


    /**
     * On récupère le paramètre dans l'URL qui s'appel filtre
     *
     * On récupère le manager de Doctrine
     *
     * Initialise le billet à null
     * Si le filtre existe et si il vaut "billet" alors
     * on demande au manager de rechercher un objet de la classe billet via son id
     * l'id étant récupéré depuis l'URL
     *
     * On récupère le répo de la classe "Commentaire"
     * On demande au repo de récupérer la liste des commentaires via un filtre et un billet si ils existent
     *
     * On prépare les paramètres pour la vue en ajoutant les commentaires
     * Si billet existe depuis la BD  alors
     * on ajoute ce billet dans la liste des paramètres de la vue
     *
     * On affiche la vue liste des commentaires
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function listeCommentairesAction(Request $request)
    {
        $filtre = $request->query->get('filtre');

        $em = $this->getDoctrine()->getManager();

        $billet = null;
        if (isset($filtre) && $filtre == 'billet') {
            $billet = $em->find('ALTAppBundle:Billet', $request->query->get('id'));
        }

        $repository = $em->getRepository('ALTAppBundle:Commentaire');
        $commentaires = $repository->listeCommentaires($filtre, $billet);

        $parametres = ['commentaires' => $commentaires];
        if (isset($billet)) {
            $parametres['billet'] = $billet;
        }

        return $this->render('ALTAppBundle:Admin:liste_commentaires.html.twig', $parametres);
    }

    /**
     * On récupère le manager de Doctrine
     * On supprime l'entité $contact
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     *
     * Redirection vers la liste des contacts
     *
     * @param Contact $contact
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerContactAction(Contact $contact)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();

        $this->addFlash('notice', 'Le ccontact a bien été supprimé !');

        return $this->redirectToRoute('alt_app_admin_liste_contacts');
    }

    /**
     *On récupère le manager de Doctrine
     *On supprime l'entité $commentaire
     *On demande au manager de mettre à jour la base de données en prenant en compte les changements
     *
     * Redirection vers la liste des commentaires
     *
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerCommentaireAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();

        $this->addFlash('notice', 'Le commentaire a bien été supprimé !');

        return $this->redirectToRoute('alt_app_admin_liste_commentaires');
    }

    /**
     *On récupère le manager de Doctrine
     *On dit à l'objet "billetPublie" d'activer la publication de celui ci(publie à vrai)
     *On demande au manager de mettre à jour la base de données en prenant en compte les changements
     *
     *Redirection vers la liste des billets
     *
     * @param Billet $billetPublie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publierBilletAction(Billet $billetPublie)
    {
        //On récupère le manager pour dialoguer avec la base de données -> On dit à l'objet "billetPublie" d'activer la publication de celui ci(publie à vrai) ->Et on déclenche l'enregistrement
        $em = $this->getDoctrine()->getManager();
        $billetPublie->setPublier(true);
        $em->flush();

        $this->addFlash('notice', 'Le billet a bien été publié !');

        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    /**
     *On récupère le manager de Doctrine
     *On dit à l'objet "billetPublie" de desactiver la publication de celui ci(publie à faux)
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     *
     *Redirection vers la liste des billets
     *
     * @param Billet $billetPublie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function depublierBilletAction(Billet $billetPublie)
    {
        //On récupère le manager pour dialoguer avec la base de données -> On dit à l'objet "billetPublie" de desactiver la publication de celui ci(publie à faux)->Et on déclenche l'enregistrement
        $em = $this->getDoctrine()->getManager();
        $billetPublie->setPublier(false);
        $em->flush();

        $this->addFlash('notice', 'Le billet a bien été dépublié !');

        return $this->redirectToRoute('alt_app_admin_liste_billets');
    }

    /**
     *Modification d'un commentaire et validation de celui-ci
     *Soit direction vers la liste des commentaires / Soit retour à la page de modification de commentaire
     *
     * @param Commentaire $commentaire
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifierCommentaireAction(Commentaire $commentaire, Request $request)
    {
        //Externalisation du formulaire dans Form CommentaireType
        $form = $this->get('form.factory')->create(CommentaireType::class, $commentaire);

        // Si la requête est en POST -> On fait le lien Requête <-> Formulaire -> la variable $commentaire contient les valeurs entrées dans le formulaire par le visiteur
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont soumisent et correctes, On récupère le manager pour dialoguer avec la base de données ->On modifie le signale à faux ->Et on déclenche l'enregistrement
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $commentaire->setSignale(false);
                $em->flush();
            }

            $this->addFlash('notice', 'Le commentaire a bien été modifié !');

            return $this->redirectToRoute('alt_app_admin_liste_commentaires');
        }

        // À ce stade, le formulaire n'est pas valide car Soit la requête est de type GET / Soit la requête est de type POST, mais le formulaire contient des valeurs invalides
        return $this->render('ALTAppBundle:Admin:modifier.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Permet de valider un commentaire qui a été signalé
     * Redirection vers la liste des commentaires
     *
     * @param Commentaire $commentaireValider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validerCommentaireAction(Commentaire $commentaireValider)
    {
        //On récupère le manager pour dialoguer avec la base de données ->On modifie le signale à faux ->Et on déclenche l'enregistrement
        $em = $this->getDoctrine()->getManager();
        $commentaireValider->setSignale(false);
        $em->flush();

        $this->addFlash('notice', 'Le commentaire a bien été validé !');

        return $this->redirectToRoute('alt_app_admin_liste_commentaires');
    }


    /**
     * Création d'un formulaire du type contactType
     *
     * Si la requête est en POST alors :
     * On attache les données du formulaire dans l'objet de formulaire et rempli l'objet $contact de ces données
     *
     * Si le formulaire a été soumis et qu'il est valide alors :
     * Date à la date du jour
     * On récupère le manager de Doctrine
     * On demande au manager de garder en mémoire l'objet $billet
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     *
     * Création de l'e-mail avec SwiftMailer
     *
     * On redirige l'utilisateur vers une autre route
     *
     * Sinon, on affiche la vue contenant le formulaire et ses possibles erreurs si il y en a.
     *
     * @param Contact $contact
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function reponseContactAction(Contact $contact, Request $request)
    {
        $form = $this->get('form.factory')->create(ContactType::class, $contact);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $contact->setDateReponse(new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')//Message en HTML
                    ->setSubject("Réponse à : " . $contact->getSujet())//Email et le titre du mail devient le sujet de mon objet contact
                    ->setFrom($this->getParameter('mailer_user'))// Email de l'expéditeur - nous
                    ->setTo($contact->getEmail())// destinataire du mail
                    ->setBody($this->renderView('@ALTApp/Admin/mail_reponse_contact.html.twig', array(
                        'contact' => $contact
                    ))); // contenu réponse du mail + contenu contact

                $this->get('mailer')->send($message);//Envoi mail
            }

            $this->addFlash('notice', 'Le réponse a bien été envoyée !');

            return $this->redirectToRoute('alt_app_admin_liste_contacts');
        }

        return $this->render('ALTAppBundle:Admin:reponseContact.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));

    }

    /**
     * Affiche la page Lecture de la réponse faite au contact
     *
     * @param Contact $contact
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lectureContactReponseAction(Contact $contact)
    {
        return $this->render('ALTAppBundle:Admin:lecture_contact_reponse.html.twig', array(
            'contact' => $contact,
        ));
    }
}
