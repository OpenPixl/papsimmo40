<?php

namespace App\Form\Webapp;

use App\Entity\Webapp\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('slug')
            ->add('content')
            ->add('isShowtitle')
            ->add('isShowdate')
            ->add('isShowreadmore')
            ->add('isLink')
            ->add('linkText')
            ->add('state')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('category')
            ->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
