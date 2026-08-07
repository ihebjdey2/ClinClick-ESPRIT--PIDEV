<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Prescription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PrescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('medication', TextType::class, [
                'label' => 'Médicament',
                'attr' => ['maxlength' => 160, 'autocomplete' => 'off'],
            ])
            ->add('dosage', TextType::class, [
                'label' => 'Dosage',
                'attr' => ['maxlength' => 120, 'placeholder' => 'Ex. 500 mg'],
            ])
            ->add('frequency', TextType::class, [
                'label' => 'Fréquence et durée',
                'attr' => ['maxlength' => 160, 'placeholder' => 'Ex. 2 fois par jour pendant 5 jours'],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Début du traitement',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Fin du traitement (facultative)',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false,
            ])
            ->add('instructions', TextareaType::class, [
                'label' => 'Instructions complémentaires',
                'required' => false,
                'attr' => ['rows' => 4, 'maxlength' => 500],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prescription::class,
        ]);
    }
}
