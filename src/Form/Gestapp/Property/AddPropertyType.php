<?php

namespace App\Form\Gestapp\Property;

use App\Form\Model\AddPropertyModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AddPropertyType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomandat', CheckboxType::class, [
                'label' => 'Est-ce un bien sans mandat ?'
            ])
            ->add('mandat', IntegerType::class, [
                'label' => 'Mandat'
            ])
            ->add('mandat', IntegerType::class, [
                'label' => 'Mandat'
            ])
            ->add('type_mandat', ChoiceType::class, [
                'label' => 'Type de mandat',
                'choices' => [
                    'Sans exclusivité' => 'sans_exclusivité',
                    'Avec semi-exclusivité' => 'avec_semi-exclusivité',
                    'Avec exclusivité' => 'avec_exclusivité',
                    'Avec exclusivité vente interactive' => 'avec_exclusivité_vente_interactive',
                ],
                'choice_attr' => [
                    'Sans exclusivité' => ['data-data' => 'sans_exclusivité'],
                    'Avec semi-exclusivité' => ['data-data' => 'avec_semi-exclusivité'],
                    'Avec exclusivité' => ['data-data' => 'avec_exclusivité'],
                    'Avec exclusivité vente interactive' => ['data-data' => 'avec_exclusivité_vente_interactive'],
                    ]
            ])
            ->add('destination', ChoiceType::class, options: [
                'label' => 'Destination du bien',
                'choices' => [
                    'Vente pour particulier' => 81,
                    'Location pour particulier' => 51,
                    'Vente pour professionnel' => 49,
                    'Location pour professionnel' => 48,
                ],
                'choice_attr' => [
                    'Vente pour particulier' => ['data-data' => 81],
                    'Location pour particulier' => ['data-data' => 51],
                    'Vente pour professionnel' => ['data-data' => 49],
                    'Location pour professionnel' => ['data-data' => 48],
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => AddPropertyModel::class,
            'csrf_protection' => true
        ]);
    }
}
