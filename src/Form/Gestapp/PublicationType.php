<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isWebpublish')
            ->add('isSocialNetwork')
            ->add('sector')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
