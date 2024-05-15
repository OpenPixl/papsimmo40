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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CustomerType extends AbstractType
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
                    'Particulier' => ['data-data' => 'Particulier'],
                    'Société' => ['data-data' => 'Société'],
                ],
            ])
            ->add('nameStructure', TextType::class, [
                'label' => 'Nom de la structure',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'attr' => [
                    'class' => 'radio-inline'
                ],
                'choices'  => [
                    'M.' => 1,
                    "Mme" => 2,
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Nom & Prénom',
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => false
            ])
            ->add('maidenName', TextType::class, [
                'label' => 'Nom de jeune fille',
                'required' => false
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
                'required' => false
            ])
            ->add('otherEmail', TextType::class, [
                'label' => 'Email',
                'required' => false
            ])
            ->add('facebook', TextType::class, [
                'label' => 'Réseaux sociaux',
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
            ->add('customerChoice', EntityType::class, [
                'class' => CustomerChoice::class,
                'required' => false,
                'label' => 'Type de client',

            ])
            ->add('isArchived', CheckboxType::class, [
                'required' => false,
                'label' => "Client archivé ?"
            ])
            ->add('cifilename', FileType::class,[
                'label' => "Le document ne doit pas dépasser 10Mo de taille",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10000k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Attention, veuillez charger un fichier au format jpg ou png',
                    ])
                ],
            ])
            ->add('kbisfilename', FileType::class,[
                'label' => "Le document ne doit pas dépasser 10Mo de taille",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10000k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Attention, veuillez charger un fichier au format jpg ou png',
                    ])
                ],
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
