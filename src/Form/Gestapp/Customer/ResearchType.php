<?php

namespace App\Form\Gestapp\Customer;

use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Customer\Research;
use App\Entity\Gestapp\Customer\ResearchOptions;
use App\Entity\Gestapp\Customer\ResearchBien;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('researchFor', ChoiceType::class, [
                'label' => 'Choix de recherche',
                'attr' => [
                    'class' => 'radio-inline'
                ],
                'choices'  => [
                    'ACHAT' => 'achat',
                    "LOCATION" => 'location',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('ResearchBien', EntityType::class, [
                'class' => ResearchBien::class,
                //'help' => 'Seule la première source d\'énérgie sera publiée sur les diffuseurs',
                'label' => 'Type de bien',
                'multiple' => false,
                'choice_attr' => function (ResearchBien $ResearchBien, $key, $index) {
                    return ['data-data' => $ResearchBien->getName() ];
                },
            ])
            ->add('typeProject', ChoiceType::class, [
                'label' => 'Le projet',
                'attr' => [
                    'class' => 'radio-inline'
                ],
                'choices'  => [
                    'De l\'immobilier neuf' => 'neuf',
                    'Dans de l\'ancien' => 'ancien',
                    "Pour un Projet de construction" => 'construction',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('budgetMin')
            ->add('budgetMax')
            ->add('pieceMin')
            ->add('pieceMax')
            ->add('roomMin')
            ->add('roomMax')
            ->add('surfaceMin')
            ->add('surfaceMax')
            ->add('surfaceLandMin')
            ->add('surfaceLandMax')
            ->add('energies', EntityType::class, [
                'class' => PropertyEnergy::class,
                'help' => 'Seule la première source d\'énérgie sera publiée sur les diffuseurs',
                'label' => 'Energies',
                'multiple' => true,
                'choice_attr' => function (PropertyEnergy $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                },
            ])
            ->add('options', EntityType::class, [
                'class' => ResearchOptions::class,
                'help' => 'Seule la première source d\'énérgie sera publiée sur les diffuseurs',
                'label' => 'Options',
                'multiple' => true,
                'choice_attr' => function (ResearchOptions $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                },
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Annonce',
                'required' => false,
                'empty_data' =>''
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Research::class,
        ]);
    }
}
