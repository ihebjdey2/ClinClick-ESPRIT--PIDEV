<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Consultation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('consultedAt', DateTimeType::class, [
                'label' => 'Date et heure de consultation',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'help' => 'Cette date doit correspondre à la consultation réellement effectuée.',
            ])
            ->add('diagnosis', TextType::class, [
                'label' => 'Diagnostic ou constat principal',
                'attr' => ['maxlength' => 255, 'autocomplete' => 'off'],
            ])
            ->add('clinicalNotes', TextareaType::class, [
                'label' => 'Notes cliniques',
                'help' => 'Information confidentielle visible uniquement par le patient et son médecin assigné.',
                'attr' => ['rows' => 8, 'maxlength' => 5000, 'autocomplete' => 'off'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}
