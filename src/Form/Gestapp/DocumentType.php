<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\choice\CatDocument;
use App\Entity\Gestapp\Document;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du document'
            ])
            ->add('category', EntityType::class, [
                'label'=> 'Catégorie',
                'class' => CatDocument::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_attr' => function (CatDocument $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                }
            ])
            ->add('typeDoc', ChoiceType::class, [
                'label' => 'Format de fichier',
                'choices'  => [
                    'Pdf' => "Pdf",
                    'Traitement de texte' => 'Word',
                    'tableur' => 'Excel',
                    'Vidéo' => 'Mp4',
                ],
                'choice_attr' => [
                    'Pdf' => ['data-data' => 'Pdf'],
                    'Traitement de texte' =>['data-data' => 'Word'],
                    'tableur' => ['data-data' => 'Excel'],
                    'Vidéo' => ['data-data' => 'Mp4']
                ],
            ])
            ->add('pdfFilename', FileType::class, [
                'label' => 'Fichier PDF',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un fichier au format PDF',
                    ])
                ],
            ])
            ->add('wordFilename', FileType::class, [
                'label' => 'Fichier docx, doc, odt.',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un fichier au format PDF',
                    ])
                ],
            ])
            ->add('excelFilename', FileType::class, [
                'label' => 'Fichier xlsx, xls, odc',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un fichier au format PDF',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
