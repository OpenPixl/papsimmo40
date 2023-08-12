<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('refMandate')
            ->add('slug')
            ->add('state')
            ->add('notes')
            ->add('mandateLength')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('refEmployed')
            ->add('mandateType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
