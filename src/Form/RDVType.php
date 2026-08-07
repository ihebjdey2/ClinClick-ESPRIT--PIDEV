<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CategoryR;
use App\Entity\RDV;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RDVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Motif',
                'attr' => ['placeholder' => 'Ex. Consultation de suivi'],
            ])
            ->add('doctor', EntityType::class, [
                'class' => User::class,
                'query_builder' => static fn (UserRepository $repository) => $repository->createByBusinessRoleQueryBuilder(User::ROLE_DOCTOR),
                'choice_label' => static fn (User $doctor): string => 'Dr '.$doctor->getFullName(),
                'label' => 'Médecin',
                'placeholder' => 'Sélectionner un médecin',
            ])
            ->add('scheduledAt', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['min' => (new DateTimeImmutable('+5 minutes'))->format('Y-m-d\TH:i')],
            ])
            ->add('durationMinutes', ChoiceType::class, [
                'label' => 'Durée',
                'choices' => ['15 minutes' => 15, '30 minutes' => 30, '45 minutes' => 45, '60 minutes' => 60],
            ])
            ->add('category', EntityType::class, [
                'class' => CategoryR::class,
                'choice_label' => 'nom',
                'label' => 'Type de rendez-vous',
                'placeholder' => 'Sélectionner un type',
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Note administrative (facultatif)',
                'required' => false,
                'attr' => ['rows' => 4],
                'help' => "N'indiquez pas de diagnostic ni de donnée médicale sensible dans ce champ.",
            ]);

        if ($options['show_patient']) {
            $builder->add('patient', EntityType::class, [
                'class' => User::class,
                'query_builder' => static fn (UserRepository $repository) => $repository->createByBusinessRoleQueryBuilder(User::ROLE_PATIENT),
                'choice_label' => static fn (User $patient): string => $patient->getFullName().' — '.$patient->getEmail(),
                'label' => 'Patient',
                'placeholder' => 'Sélectionner un patient',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RDV::class,
            'show_patient' => false,
        ]);
        $resolver->setAllowedTypes('show_patient', 'bool');
    }
}
