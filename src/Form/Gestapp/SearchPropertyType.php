<?php

namespace App\Form\Gestapp;

use App\Entity\Admin\Employed;
use App\Form\Model\SearchPropertyModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('refmandat', SearchType::class, [
                'required' => false,
            ])
            ->add('zipcode', SearchType::class, [
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