<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref')
            ->add('name')
            ->add('annonce')
            ->add('piece')
            ->add('room')
            ->add('isHome')
            ->add('isApartment')
            ->add('isLand')
            ->add('isOther')
            ->add('otherDescription')
            ->add('surfaceLand')
            ->add('surfaceHome')
            ->add('dpeAt', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => false,
                    // this is actually the default format for single_text
                    'format' => 'dd-MM-yyyy',
                    'attr' => ['class' => 'js-datepicker form-control form-control-sm'],
                ])
            ->add('diagDpe')
            ->add('diagGpe')
            ->add('adress')
            ->add('complement')
            ->add('zipcode')
            ->add('city')
            ->add('notaryEstimate')
            ->add('applicantEstimate')
            ->add('cadasterZone')
            ->add('cadasterNum')
            ->add('cadasterSurface')
            ->add('cadasterCariez')
            ->add('refEmployed')
            ->add('options')
            ->add('publication')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
