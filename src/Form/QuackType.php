<?php

namespace App\Form;

use App\Entity\Quack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Tag;
class QuackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'label' => 'Message',
                'attr' => ['placeholder' => 'Écrivez votre message ici...'],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'required' => false,
                'mapped' => false, // Le champ photo n'est pas mappé directement à l'entité
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true, // Permet de sélectionner plusieurs tags
                'expanded' => true, // Affiche les options comme des cases à cocher
                'label' => 'Tags associés',
            ]);
//            ->add('parent', EntityType::class, [
//                'class' => Quack::class,
//                'choice_label' => 'content', // Affiche le contenu du quack parent
//                'required' => false, // Permet de ne pas avoir de parent (quack principal)
//                'placeholder' => 'Aucun parent', // Texte par défaut si aucun parent n'est sélectionné
//                'label' => 'Quack parent (optionnel)',
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quack::class,
        ]);
    }
}
