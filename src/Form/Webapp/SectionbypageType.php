<?php

namespace App\Form\Webapp;

use App\Entity\Webapp\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionbypageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('content', ChoiceType::class, [
                'choices'  => [
                    'aucun' => 'none',
                    'ARTICLES' => [
                        'Un article' => 'One_article',
                        'Une categorie' => 'CategoryProduct',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
