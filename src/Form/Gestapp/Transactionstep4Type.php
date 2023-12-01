<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\choice\PropertyBanner;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Transaction;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class Transactionstep4Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateAtSale', DateType::class, [
                'label' => "Rendez-vous pour la signature de l'acte de vente.",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
            ])
            ->add('actePdfFilename', FileType::class,[
                'label' => "Déposer le dossier PDF de l'acte, le fichier ne doit pas dépasser 10Mo de taille",
                'mapped' => false,
                //'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10238k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
