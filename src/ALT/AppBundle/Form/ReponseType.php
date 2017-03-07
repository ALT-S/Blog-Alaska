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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Classe qui gère le formulaire d'ajout et modification d'un commentaire
 * Dans le cas où on veut modifier un commentaire, on ajoute un champ "date" supplémentaire.
 */
class ReponseType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->getData()->getId() !== null) {
            $builder->add('date', DateType::class);
        }
        
        $builder
            ->add('contenu',   TextareaType::class, array('required' => false))
            ->add('auteur', TextType::class)
            ->add('enregistrer',      SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Commentaire::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}