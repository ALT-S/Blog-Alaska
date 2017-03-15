<?php

namespace ALT\AppBundle\Controller;

use ALT\AppBundle\Entity\Contact;
use ALT\AppBundle\Form\DemandeContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * Création de l'entité - objet Contact
     * 
     * Création d'un formulaire du type DemandeContactType
     * 
     * Si la requête est en POST alors :
     * On attache les données du formulaire dans l'objet de formulaire et rempli l'objet $contact de ces données
     * 
     * Si le formulaire a été soumis et qu'il est valide alors :
     * On récupère le manager de Doctrine
     * On demande au manager de garder en mémoire l'objet $contact
     * On demande au manager de mettre à jour la base de données en prenant en compte les changements
     * 
     * Création de l'e-mail avec SwiftMailer
     * 
     * On redirige l'utilisateur vers une autre route
     *
     * Sinon, on affiche la vue contenant le formulaire et ses possibles erreurs si il y en a.
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();
        
        $formContact = $this->get('form.factory')->create(DemandeContactType::class, $contact);
        
        if ($request->isMethod('POST')) {
            $formContact->handleRequest($request);
            
            if ($formContact->isSubmitted() && $formContact->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();
                
                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')//Message en HTML
                    ->setSubject("Contact de :" . $contact->getEmail() . " : " . $contact->getSujet())//Email et le titre du mail devient le sujet de mon objet contact
                    ->setFrom($this->getParameter('mailer_user'))// Email de l'expéditeur est le destinataire du mail
                    ->setTo($this->getParameter('mailer_user'))// destinataire du mail
                    ->setBody($contact->getContenu()); // contenu du mail
                
                $this->get('mailer')->send($message);//Envoi mail
                
                $this->addFlash('notice', 'Votre message a bien été envoyé !');
                
                return $this->redirectToRoute('alt_app_contact');
            }
        }
        return $this->render('ALTAppBundle:Front:contact.html.twig', array(
            'form' => $formContact->createView(),
        ));
    }
}
