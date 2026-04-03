<?php

namespace App\Form\Gestapp\Transaction;

use App\Entity\Enum\Transaction\ActeName;
use App\Entity\Gestapp\Transaction;
use App\Entity\Gestapp\Transaction\Acte;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ActeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('acteName', EnumType::class, [
                'class' => ActeName::class,
                'choice_label' => static function (\UnitEnum $choice): string {
                    return $choice->value;
                },

                'placeholder' => 'Choisir un avenant',
            ])
            ->add('acteFile', FileType::class,[
                'label' => "L'avenant ne doit pas dépasser 20Mo de taille",
                'mapped' => false,
                'required' => false,
                'help' => 'Veuillez choisir un fichier au format PDF de moins de 20Mo',
                'constraints' => [
                    new File([
                        'maxSize' => '20000k',
                        'mimeTypes' => [
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
            'data_class' => Acte::class,
        ]);
    }
}
