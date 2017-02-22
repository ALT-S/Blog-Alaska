<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 22/02/2017
 * Time: 21:07
 */

namespace ALT\AppBundle\Form;


use ALT\AppBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Classe qui gère le formulaire de réponse à un contact
 * 
 */
class ContactType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenuReponse',      TextareaType::class)
            ->add('envoyer',      SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Contact::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}