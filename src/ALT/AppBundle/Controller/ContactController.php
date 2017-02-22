<?php

namespace ALT\AppBundle\Controller;

use ALT\AppBundle\Entity\Contact;
use ALT\AppBundle\Form\DemandeContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();// Création de l'entité - objet Contact
        $contact->setDate(new \Datetime());// On préremplit date du jour

        //Externalisation du formulaire dans Form DemandeContact
        $formContact = $this->get('form.factory')->create(DemandeContactType::class, $contact);

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $billet contient les valeurs entrées dans le formulaire par le visiteur
            $formContact->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($formContact->isSubmitted() && $formContact->isValid()) {
                // On enregistre notre objet $contact dans la base de données
                $em = $this->getDoctrine()->getManager();//On récupère le manager pour dialoguer avec la base de données
                $em->persist($contact);// puis on « persiste » l'entité, garde cette entité en mémoire
                $em->flush();// Et on déclenche l'enregistrement

                // Création de l'e-mail avec SwiftMailer
                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')//Message en HTML
                    ->setSubject("Contact de :".$contact->getEmail()." : ".$contact->getSujet())//Email et le titre du mail devient le sujet de mon objet contact
                    ->setFrom($this-> getParameter('mailer_user'))// Email de l'expéditeur est le destinataire du mail
                    ->setTo($this-> getParameter('mailer_user')) // destinataire du mail
                    ->setBody($contact->getContenu()); // contenu du mail

                //Envoi mail
                $this->get('mailer')->send($message);

                // Création du « flashBag » qui contient les messages flash
                $this->addFlash('notice', 'Votre message a bien été envoyé !');

                // On redirige vers la page qui va afficher contact
                return $this->redirectToRoute('alt_app_contact');
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        // Donc on affiche la page qui va afficher contact, on fait passer le paramètre formContact  dans la vue
        return $this->render('ALTAppBundle:Front:contact.html.twig', array(
            'form' => $formContact->createView(),
        ));
    }
}
