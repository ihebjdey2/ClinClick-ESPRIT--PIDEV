<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Evenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image (JPG, PNG ou WebP — 2 Mo maximum)',
                'required' => $options['image_required'],
                'mapped' => true,
                'constraints' => [
                    new Assert\File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Veuillez sélectionner une image JPG, PNG ou WebP valide.'
                    ),
                ],
            ])
            ->add('date', DateType::class, [
                'label' => "Date de l'événement",
                'widget' => 'single_text',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
            'image_required' => false,
        ]);
        $resolver->setAllowedTypes('image_required', 'bool');
    }
}
