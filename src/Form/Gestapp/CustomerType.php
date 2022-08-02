<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Choice\CustomerChoice;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('complement', TextType::class, [
                'label' => 'Complément'
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal'
            ])
            ->add('city', TextType::class, [
                'label' => 'Commune'
            ])
            ->add('home', TextType::class, [
                'label' => 'Tel Personnel'
            ])
            ->add('desk', TextType::class, [
                'label' => 'Tel Portable'
            ])
            ->add('gsm', TextType::class, [
                'label' => 'Tel Bureau'
            ])
            ->add('fax', TextType::class, [
                'label' => 'Fax'
            ])
            ->add('otherEmail', TextType::class, [
                'label' => 'Email'
            ])
            ->add('facebook', TextType::class, [
                'label' => 'Page Facebook'
            ])
            ->add('instagram', TextType::class, [
                'label' => 'Page instagram'
            ])
            ->add('linkedin', TextType::class, [
                'label' => 'Page linkedin'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
