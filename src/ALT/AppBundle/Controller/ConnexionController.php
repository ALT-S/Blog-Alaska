<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 13/02/2017
 * Time: 15:26
 */
namespace ALT\AppBundle\Controller;


use ALT\AppBundle\Entity\Connexion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class ConnexionController extends Controller
{


    public function connexionAction(Request $request)
    { // Création de l'entité - objet Billet
        $connexion = new Connexion();
        // préremplit date du jour
        $connexion->setDate(new \Datetime());
        //Création du formulaire "FormBuilder" par le service form factory
        $formConnexion= $this->get('form.factory')->createBuilder(FormType::class, $connexion)
            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            ->add('date',      DateType::class)
            ->add('nom',    TextType::class)
            ->add('prenom',    TextType::class)
            ->add('email',    EmailType::class)
            ->add('mot de passe',     PasswordType::class)
            ->add('enregistrer',      SubmitType::class)
            ->getForm()
        ;

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $billet contient les valeurs entrées dans le formulaire par le visiteur
            $formConnexion->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($formConnexion->isSubmitted() && $formConnexion->isValid()) {
                // On enregistre notre objet $billet dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($formConnexion);
                $em->flush();


                $this->addFlash('notice', 'Bonjour !');

                // On redirige vers la page de visualisation du billet crée
                return $this->redirectToRoute('alt_app_homepage');
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('@ALTApp/Front/connexion.html.twig', array(
            'form' => $formContact->createView(),
        ));
    }


}