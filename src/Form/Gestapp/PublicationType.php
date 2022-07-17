<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isWebpublish', CheckboxType::class, [
                'label' => 'publié sur le site ?',
                'required' => false
            ])
            ->add('isSocialNetwork', CheckboxType::class, [
                'label' => 'publié sur les réseaux ?',
                'required' => false
            ])
            ->add('sector', ChoiceType::class, [
                'label' => 'secteur',
                'choices'  => [
                    'Mont de marsan & alentours' => "mdm-alentours",
                    'Dax & alentours' => 'dax-alentours'
                ],
                'choice_attr' => [
                    'Mont de marsan & alentours' => ['data-data' => 'Mont de marsan & alentours'],
                    'Dax & alentours' => ['data-data' => 'Dax & alentours']
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
