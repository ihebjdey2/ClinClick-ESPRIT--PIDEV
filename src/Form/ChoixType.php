<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChoixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
            ])
            ->add('kind', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Type de stock',
                'choices' => [
                    'Médicament' => 'medicine',
                    'Équipement' => 'equipment',
                ],
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Continuer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Stock::class]);
    }
}
