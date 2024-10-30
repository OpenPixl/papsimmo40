<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Agency;
use App\Entity\Gestapp\AgencyEmployed;
use App\Entity\Gestapp\Transaction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgencyEmployedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('refAgency', EntityType::class, [
                'class' => Agency::class,
                'choice_label' => 'id',
            ])
            ->add('refTransaction', EntityType::class, [
                'class' => Transaction::class,
                'choice_label' => 'id',
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
