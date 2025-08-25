<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\choice\CustomerChoice;
use App\Entity\Gestapp\Customer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
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
                    'Un particulier' => "particulier",
                    'Un professionnel' => 'professionnel',
                    'Un dirigeant' => 'dirigeant',
                ],
                'choice_attr' => [
                    'Un particulier' => ['data-data' => 'particulier'],
                    'Un professionnel' => ['data-data' => 'professionnel'],
                    'Un dirigeant' => ['data-data' => 'dirigeant'],
                ],
            ])
            ->add('typeStructure', ChoiceType::class,[
                'label' => 'Type de client',
                'choices'  => [
                    'Une SCI' => 'sci',
                    'Une Société' => 'societe',
                    'Un entreprise Individuel' => 'professionnel',
                ],
                'choice_attr' => [
                    'Une SCI' => ['data-data' => 'sci'],
                    'Une Société' => ['data-data' => 'societe'],
                    'Une entreprise Individuel' => ['data-data' => 'ei']
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
                    'Mlle' => 3
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom & Nom',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => false,
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
            ])
            ->add('city', HiddenType::class, [
                'label' => 'Commune',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('proAdress', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('proComplement', TextType::class, [
                'label' => 'Complément',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('proZipcode', TextType::class, [
                'label' => 'Code Postal',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('proCity', HiddenType::class, [
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
            ->add('deskStructure', TextType::class, [
                'label' => 'Tel Bureau',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('gsmStructure', TextType::class, [
                'label' => 'Tel Portable',
                'required' => false
            ])
            ->add('otherEmail', TextType::class, [
                'label' => 'Email',
                'required' => false
            ])
            ->add('EmailStructure', TextType::class, [
                'label' => 'Email',
                'required' => false
            ])
            ->add('customerChoice', EntityType::class, [
                'class' => CustomerChoice::class,
                'required' => false,
                'label' => 'Type de client',
            ])
            ->add('isArchived', CheckboxType::class, [
                'required' => false,
                'label' => "Souhaitez-vous archiver la fiche ?"
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

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if(($data['customerChoice'] ?? null) === "3"){
                if (($data['typeClient'] ?? null) === 'particulier') {
                    $form
                        ->add('firstName', TextType::class, [
                            'label' => 'Prénom & Nom',
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    'message' => '- Le prénom est obligatoire'
                                ]),
                                new Assert\Regex([
                                    'pattern' => '/^([^,]*)$/',
                                    'message' => 'Le prénom ne doit pas contenir de virgule.'
                                ])
                            ],
                        ])
                        ->add('lastName', TextType::class, [
                            'label' => 'Prénom & Nom',
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    'message' => '- Le nom est obligatoire'
                                ]),
                            ],
                        ])
                        ->add('gsm', TextType::class, [
                            'label' => 'Tel Portable',
                            'constraints'=> [
                                new Assert\NotBlank([
                                    'message' => '- Un numéro de portable est obligatoire'
                                ])
                            ],
                        ])
                    ;
                    if(($data['civility'] ?? null) === '2'){
                        $form
                            ->add('maidenName', TextType::class, [
                                'label' => 'Nom de jeune fille',
                                'constraints' => [
                                    new Assert\NotBlank([
                                        'message' => '- Le nom de naissance est obligatoire'
                                    ]),
                                ],
                            ])
                        ;
                    }

                }
                if (($data['typeClient'] ?? null) === 'professionnel') {
                    $form
                        ->add('nameStructure', TextType::class, [
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    "message" => "- Le nom de la structure est obligatoire"
                                ])
                            ],
                        ])
                        ->add('gsmStructure', TextType::class, [
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    "message" => "- Le contact téléphonique est obligatoire"
                                ])
                            ],
                        ])
                    ;
                }
            }
            else{
                if (($data['typeClient'] ?? null) === 'particulier') {
                    $form
                        ->add('firstName', TextType::class, [
                            'constraints' => [
                                new Assert\NotBlank([
                                    'message' => '- Le prénom est obligatoire'
                                ]),
                                new Assert\Regex([
                                    'pattern' => '/^([^,]*)$/',
                                    'message' => '- Le prénom ne doit pas contenir de virgule.'
                                ])
                            ],
                        ])
                        ->add('lastName', TextType::class, [
                            'label' => 'Prénom & Nom',
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    'message' => '- Le nom est obligatoire'
                                ]),
                            ],
                        ])
                        ->add('ddn', DateType::class, [
                            'label' => 'Date de naissance',
                            'widget' => 'single_text',
                            'format' => 'dd/MM/yyyy',
                            // prevents rendering it as type="date", to avoid HTML5 date pickers
                            'html5' => false,
                            'required' => true,
                            'constraints'=> [
                                new Assert\NotBlank([
                                    'message' => '- La date de naissance est obligatoire'
                                ])
                            ],
                            'by_reference' => true,
                        ])
                        ->add('ddnIn', TextType::class, [
                            'label' => 'à',
                            'required' => true,
                            'constraints'=> [
                                new Assert\NotBlank([
                                    'message' => '- Le lieu de naissance est obligatoire'
                                ])
                            ],
                        ])
                        ->add('adress', TextType::class, [
                            'label' => 'Adresse',
                            'required' => true,
                            'constraints'=> [
                                new Assert\NotBlank([
                                    'message' => "- L'adresse est necessaire"
                                ])
                            ],
                            'empty_data' =>''
                        ])
                        ->add('zipcode', TextType::class, [
                            'label' => 'Code Postal',
                            'required' => true,
                            'constraints'=> [
                                new Assert\NotBlank([
                                    'message' => '- Le code postal est obligatoire'
                                ])
                            ],
                        ])
                        ->add('gsm', TextType::class, [
                            'label' => 'Tel Portable',
                            'constraints'=> [
                                new Assert\NotBlank([
                                    'message' => '- Un numéro de portable est obligatoire'
                                ])
                            ],
                        ])
                        ->add('otherEmail', TextType::class, [
                            'label' => 'Email',
                            'required' => true,
                            'constraints'=> [
                                new Assert\NotBlank([
                                    'message' => '- Un email est nécéssaire pour contacter le client'
                                ]),
                                new Assert\Email([
                                    "message" => "- L'email est invalide"
                                ])
                            ],
                        ])
                    ;
                    if(($data['civility'] ?? null) === '2'){
                        $form
                            ->add('maidenName', TextType::class, [
                                'label' => 'Nom de jeune fille',
                                'constraints' => [
                                    new Assert\NotBlank([
                                        'message' => '- Le nom de naissance est obligatoire'
                                    ]),
                                ],
                            ])
                        ;
                    }

                }
                if (($data['typeClient'] ?? null) === 'professionnel') {
                    $form
                        ->add('nameStructure', TextType::class, [
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    "message" => "- Le nom de la structure est obligatoire"
                                ])
                            ],
                        ])
                        ->add('proAdress', TextType::class, [
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    "message" => "- L'adresse est obligatoire"
                                ])
                            ],
                        ])
                        ->add('proZipcode', TextType::class, [
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    "message" => "- Le code postal est obligatoire"
                                ])
                            ],
                        ])
                        ->add('gsmStructure', TextType::class, [
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    "message" => "- Le contact téléphonique est obligatoire"
                                ])
                            ],
                        ])
                        ->add('EmailStructure', TextType::class, [
                            'required' => true,
                            'constraints' => [
                                new Assert\NotBlank([
                                    'message' => "- L'email est obligatoire"
                                ])
                            ]
                        ])
                    ;
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
