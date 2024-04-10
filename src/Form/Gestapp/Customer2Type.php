<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Choice\CustomerChoice;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Customer2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeClient', ChoiceType::class,[
                'label' => 'Type de client',
                'choices'  => [
                    'Particulier' => "particulier",
                    'Professionnel' => 'professionnel',
                ],
                'choice_attr' => [
                    'Particulier' => ['data-data' => 'particulier'],
                    'Professionnel' => ['data-data' => 'professionnel'],
                ],
            ])
            ->add('nameStructure', TextType::class, [
                'label' => 'Nom de la structure',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom & Nom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('complement', TextType::class, [
                'label' => 'Complément',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('city', HiddenType::class, [
                'label' => 'Commune',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('home', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('desk', TextType::class, [
                'label' => 'Tel Bureau',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('gsm', TextType::class, [
                'label' => 'Tel Portable',
                'required' => true
            ])
            ->add('otherEmail', TextType::class, [
                'label' => 'Email',
                'required' => true
            ])
            ->add('ddn', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
            ])
            ->add('ddnIn', TextType::class, [
                'label' => 'à',
                'required' => false,
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
