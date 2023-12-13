<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Reco;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('announceFirstName')
            ->add('announceLastName')
            ->add('announcePhone')
            ->add('announceEmail')
            ->add('customerFirstName')
            ->add('customerLastName')
            ->add('customerPhone')
            ->add('customerEmail')
            ->add('propertyAddress')
            ->add('propertyComplement')
            ->add('propertyZipcode')
            ->add('propertyCity')
            ->add('propertyLong')
            ->add('propertyLat')
            ->add('statutReco')
            ->add('createAt')
            ->add('updateAt')
            ->add('refEmployed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reco::class,
        ]);
    }
}
