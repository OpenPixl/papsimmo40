<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('RefCustomer')
            ->add('firstName')
            ->add('lastName')
            ->add('slug')
            ->add('adress')
            ->add('complement')
            ->add('zipcode')
            ->add('city')
            ->add('home')
            ->add('desk')
            ->add('gsm')
            ->add('fax')
            ->add('otherEmail')
            ->add('facebook')
            ->add('instagram')
            ->add('linkedin')
            ->add('isArchived')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('ddn')
            ->add('ddnIn')
            ->add('customerChoice')
            ->add('refEmployed')
            ->add('properties')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
