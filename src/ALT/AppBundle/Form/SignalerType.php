<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 22/02/2017
 * Time: 21:07
 */

namespace ALT\AppBundle\Form;


use ALT\AppBundle\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Classe qui gère le formulaire de réponse à un contact
 * 
 */
class SignalerType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',    EmailType::class, array('constraints' =>(array(new Email())))  )
            ->add('sujet',     TextType::class)
            ->add('contenu',   TextareaType::class)
            ->add('enregistrer',      SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => ::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}