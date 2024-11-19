<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PropertyImageType extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $url){

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('images', FileType::class,[
                'label' => "La photo ne doit pas dépasser 20Mo de taille",
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '20M',
                                'mimeTypes' => [
                                    'image/png',
                                    'image/jpeg',
                                    'image/jpg',
                                ],
                                'mimeTypesMessage' => 'Veuillez téléverser des fichiers JPEG, JPG, ou PNG uniquement.',
                                'maxSizeMessage' => 'La taille maximale autorisée est de 20 000 Mo par fichier.',
                            ]),
                        ],
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
