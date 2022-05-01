<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Complement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('banner')
            ->add('location')
            ->add('disponibility')
            ->add('disponibilityAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
                ])
            ->add('constructionAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
            ])
            ->add('propertyTax')
            ->add('orientation')
            ->add('houseState')
            ->add('level')
            ->add('jointness')
            ->add('washroom')
            ->add('bathroom')
            ->add('wc')
            ->add('terrace')
            ->add('balcony')
            ->add('sanitation')
            ->add('isFurnished')
            ->add('energy')
            ->add('denomination')
            ->add('houseType')
            ->add('apartmentType')
            ->add('landType')
            ->add('tradeType')
            ->add('buildingEquipment')
            ->add('houseEquipment')
            ->add('otherOption')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Complement::class,
        ]);
    }
}
