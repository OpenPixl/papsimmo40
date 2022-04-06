<?php

namespace App\Form\Admin;

use App\Entity\Admin\Employed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('firstName')
            ->add('lastName')
            ->add('slug')
            ->add('sector')
            ->add('isVerified')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('referent')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employed::class,
        ]);
    }
}
