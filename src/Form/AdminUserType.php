<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordConstraints = [new Assert\Length(
            min: 12,
            max: 4096,
            minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'
        )];
        if ($options['password_required']) {
            $passwordConstraints[] = new Assert\NotBlank(message: 'Le mot de passe est obligatoire.');
        }

        $builder
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
            ->add('dateNaissance', DateType::class, ['label' => 'Date de naissance', 'widget' => 'single_text'])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => ['Femme' => 'femme', 'Homme' => 'homme', 'Non précisé' => 'non_specifie'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles métier',
                'choices' => [
                    'Administrateur' => User::ROLE_ADMIN,
                    'Médecin' => User::ROLE_DOCTOR,
                    'Réceptionniste' => User::ROLE_RECEPTIONIST,
                    'Patient' => User::ROLE_PATIENT,
                ],
                'multiple' => true,
                'expanded' => true,
                'constraints' => [new Assert\Count(min: 1, minMessage: 'Sélectionnez au moins un rôle métier.')],
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Compte vérifié et actif',
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => $options['password_required'],
                'first_options' => [
                    'label' => $options['password_required'] ? 'Mot de passe' : 'Nouveau mot de passe (facultatif)',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                'constraints' => $passwordConstraints,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'password_required' => false,
        ]);
        $resolver->setAllowedTypes('password_required', 'bool');
    }
}
