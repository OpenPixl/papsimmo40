<?php

namespace App\Form\Webapp;

use App\Entity\Webapp\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la page'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('isShowtitle',CheckboxType::class, [
                'required' => false,
            ])
            ->add('isShowdate',CheckboxType::class, [
                'required' => false,
            ])
            ->add('isMenu',CheckboxType::class, [
                'required' => false,
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'Etat',
                'choices'  => [
                    'Brouillon' => "brouillon",
                    'Archivée' => 'archivee',
                    'Publiée' => 'publiée'
                ],
                'choice_attr' => [
                    'Brouillon' => ['data-data' => 'Brouillon'],
                    'Archivée' => ['data-data' => 'Archivée'],
                    'Publiée' => ['data-data' => 'Publiée'],
                ],
            ])
            //->add('metaKeywords')
            ->add('MetaDescrition')
            //->add('tag')
            ->add('seoTitle', TextType::class, [
                'label' => 'Balise Titre'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
