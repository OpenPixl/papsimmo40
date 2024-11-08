<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Agency;
use App\Entity\Gestapp\AgencyEmployed;
use App\Entity\Gestapp\Transaction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgencyEmployedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('refAgency', EntityType::class, [
                'label' => 'Agence',
                'class' => Agency::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AgencyEmployed::class,
        ]);
    }
}
