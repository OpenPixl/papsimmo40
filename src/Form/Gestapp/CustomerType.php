<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\choice\CustomerChoice;
use App\Entity\Gestapp\Customer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('city', TextType::class, [
                'label' => 'Commune',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('home', TextType::class, [
                'label' => 'Tel Personnel',
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
                'required' => false,
            ])
            ->add('fax', TextType::class, [
                'label' => 'Fax',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('otherEmail', TextType::class, [
                'label' => 'Email',
                'required' => false
            ])
            ->add('facebook', TextType::class, [
                'label' => 'Page Facebook',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('instagram', TextType::class, [
                'label' => 'Page instagram',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('linkedin', TextType::class, [
                'label' => 'Page linkedin',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('isArchived', CheckboxType::class, [
                'label' => 'Archiver le client ?'
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
                'label' => 'à'
            ])
            ->add('customerChoice', EntityType::class, [
                'label' => 'Type de Client',
                'class' => CustomerChoice::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_attr' => function (CustomerChoice $customerChoice, $key, $index) {
                    return ['data-data' => $customerChoice->getName() ];
                }
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
