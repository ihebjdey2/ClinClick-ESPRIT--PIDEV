<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CategorieReclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategorieReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nom', TextType::class, [
            'label' => 'Nom',
            'attr' => ['placeholder' => 'Ex. Accueil ou facturation'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CategorieReclamation::class]);
    }
}
