<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Agency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('complement', TextType::class, [
                'label' => 'Complement',
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'CP',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('contactPhone', TextType::class, [
                'label' => 'Tél'
            ])
            ->add('contactEmail', TextType::class, [
                'label' => 'Email'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agency::class,
        ]);
    }
}
