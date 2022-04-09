<?php

namespace App\Form\Admin;

use App\Entity\Admin\Employed;
use App\Repository\Admin\EmployedRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            //->add('roles')
            //->add('password')
            ->add('firstName')
            ->add('lastName')
            //->add('slug')
            ->add('sector')
            ->add('isVerified')
            ->add('referent', EntityType::class, [
                'class' => Employed::class,
                'placeholder' => '--- Aucun collorateur responsable ---',
                'query_builder' => function(EmployedRepository $employedRepository){
                    return $employedRepository->createQueryBuilder('e')->orderBy('e.lastName', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employed::class,
        ]);
    }
}
