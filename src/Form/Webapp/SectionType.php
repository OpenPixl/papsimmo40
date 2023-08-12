<?php

namespace App\Form\Webapp;

use App\Entity\Webapp\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('content', ChoiceType::class, [
                'choices'  => [
                    'aucun' => 'none',
                    'ARTICLES' => [
                        'Un article' => 'One_article',
                        'Un article en pdf' => 'article_pdf',
                        'Une categorie' => 'Category',
                    ],
                    'Collaborateur' =>[
                        "un collaborateur" => "One_Employed",
                        "Une équipe" => "One_staff",
                        "Liste de tous les collaborateurs" => "All_Employed"
                    ],
                    'Propriétés' => [
                        'Liste des proporiétés' => 'All_properties',
                        "liste des avis" => "Sector_ofProperties",
                        'Les dernières propriétés' => 'Last_property'
                    ],
                ],
            ])
            ->add('isShowtitle')
            ->add('isShowdescription')
            ->add('isShowdate')
            ->add('isfavorite')
            ->add('isSectionfluid')
            ->add('baliseClass')
            ->add('baliseId')
            ->add('baliseName')
            ->add('oneArticle')
            ->add('OneCategory')
            ->add('oneEmployed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
