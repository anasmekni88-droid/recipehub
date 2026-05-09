<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\CategorieRecette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // 🍽 TITLE
            ->add('titre', TextType::class, [
                'label' => 'Titre de la recette',
                'attr' => [
                    'placeholder' => 'Ex: Pizza maison'
                ]
            ])

            // 📝 DESCRIPTION
            ->add('description', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Décrivez brièvement votre recette...',
                    'rows' => 3
                ]
            ])

            // 👨‍🍳 INSTRUCTIONS
            ->add('instructions', TextareaType::class, [
                'label' => 'Instructions',
                'attr' => [
                    'placeholder' => 'Étape 1... Étape 2...',
                    'rows' => 7
                ]
            ])

            // ⏱ PREPARATION TIME
            ->add('tempsPreparation', IntegerType::class, [
                'label' => 'Temps de préparation (min)',
                'attr' => [
                    'min' => 1
                ]
            ])

            // 🔥 COOKING TIME
            ->add('tempsCuisson', IntegerType::class, [
                'label' => 'Temps de cuisson (min)',
                'required' => false,
                'attr' => [
                    'min' => 0
                ]
            ])

            // ⚡ DIFFICULTY
            ->add('difficulte', ChoiceType::class, [
                'label' => 'Difficulté',
                'choices' => [
                    'Facile' => 'facile',
                    'Moyen' => 'moyen',
                    'Difficile' => 'difficile',
                ],
                'placeholder' => 'Choisir une difficulté'
            ])

            // 👥 PEOPLE
            ->add('nbPersonnes', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'attr' => [
                    'min' => 1,
                    'max' => 50
                ]
            ])

            // 📂 CATEGORY
            ->add('categorie', EntityType::class, [
                'class' => CategorieRecette::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie'
            ])

            // 🖼 IMAGE
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la recette',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Veuillez uploader une image valide (jpg, png, webp)',
                    )
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}