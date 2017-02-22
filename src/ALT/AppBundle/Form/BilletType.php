<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 22/02/2017
 * Time: 21:07
 */

namespace ALT\AppBundle\Form;


use ALT\AppBundle\Entity\Billet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Classe qui gère le formulaire d'ajout et de modification d'un billet
 * Dans le cas où on veut modifier un billet, on ajoute un champ "date" supplémentaire.
 */
class BilletType extends AbstractType
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
            ->add('titre',     TextType::class)
            ->add('contenu',   TextareaType::class)
            ->add('publier', CheckboxType::class, array('required' => false))
            ->add('enregistrer',      SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Billet::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}