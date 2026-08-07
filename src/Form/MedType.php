<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', TextType::class, ['label' => 'Médicament'])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
            ])
            ->add('quantite', IntegerType::class, ['label' => 'Quantité totale'])
            ->add('dateExpiration', DateType::class, [
                'label' => "Date d'expiration",
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Stock::class]);
    }
}
