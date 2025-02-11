<?php

namespace App\Form\Admin\Search;

use App\Form\Model\SearchPropertyModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPropertyDashboardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projet', ChoiceType::class, [
                'label' => 'Projet',
                'attr' => [
                    'class' => 'radio-inline'
                ],
                'choices'  => [
                    'Appartement' => 38,
                    'Maison' => 48,
                    'Terrain' => 58,
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('zipcode', SearchType::class, [
                'required' => false,
            ])
            ->add('city', SearchType::class, [
                'required' => false,
            ])
            ->add('minPrice', SearchType::class, [
                'required' => false,
            ])
            ->add('maxPrice', SearchType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => SearchPropertyModel::class,
            'csrf_protection' => true
        ]);
    }
}