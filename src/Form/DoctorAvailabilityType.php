<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DoctorAvailability;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DoctorAvailabilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['show_doctor']) {
            $builder->add('doctor', EntityType::class, [
                'class' => User::class,
                'query_builder' => static fn (UserRepository $repository) => $repository->createByBusinessRoleQueryBuilder(User::ROLE_DOCTOR),
                'choice_label' => static fn (User $doctor): string => 'Dr '.$doctor->getFullName(),
                'label' => 'Médecin',
                'placeholder' => 'Sélectionner un médecin',
            ]);
        }

        $builder
            ->add('dayOfWeek', ChoiceType::class, [
                'label' => 'Jour',
                'choices' => ['Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7],
            ])
            ->add('startTime', TimeType::class, [
                'label' => 'Début',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endTime', TimeType::class, [
                'label' => 'Fin',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Disponibilité active',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DoctorAvailability::class,
            'show_doctor' => false,
        ]);
        $resolver->setAllowedTypes('show_doctor', 'bool');
    }
}
