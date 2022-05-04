<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\choice\ApartmentType;
use App\Entity\Gestapp\choice\BuildingEquipment;
use App\Entity\Gestapp\choice\Denomination;
use App\Entity\Gestapp\choice\HouseEquipment;
use App\Entity\Gestapp\choice\HouseType;
use App\Entity\Gestapp\choice\LandType;
use App\Entity\Gestapp\choice\OtherOption;
use App\Entity\Gestapp\choice\TradeType;
use App\Entity\Gestapp\Complement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('banner', ChoiceType::class, [
                'label' => 'Bannière',
                'choices'  => [
                    'Coup de coeur' => "coup-de-coeur",
                    'A saisir' => 'a-saisir',
                    'Dernière entrée' => 'derniere-entree'
                ],
                'choice_attr' => [
                    'Coup de coeur' => ['data-data' => 'Coup de coeur'],
                    'A saisir' => ['data-data' => 'A saisir'],
                    'Dernière entrée' => ['data-data' => 'Dernière entrée'],
                ],
            ])
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
            ->add('propertyTax', MoneyType::class, [
                'divisor' => 100,
                'label' => 'Taxe foncière'
            ])
            ->add('orientation', ChoiceType::class, [
                'label' => 'Orientation',
                'choices'  => [
                    'Sud' => "sud",
                    'Plein sud' => 'plein-sud',
                    'Sud-ouest' => 'Sud-ouest',
                    'ouest' => 'ouest',
                    'Est'=> 'est'
                ],
                'choice_attr' => [
                    'Sud' => ['data-data' => 'Sud'],
                    'Plein sud' => ['data-data' => 'Plein sud'],
                    'Sud-ouest' => ['data-data' => 'Sud-ouest'],
                    'ouest' => ['data-data' => 'ouest'],
                    'Est'=> ['data-data' => 'Est'],
                ],
            ])
            ->add('houseState', ChoiceType::class, [
                'label' => 'Etat du bien',
                'choices'  => [
                    'neuf' => "neuf",
                    'A rénover' => 'a-saisir',
                    'Quelques travaux' => 'quelques-travaux'
                ],
            ])
            ->add('level', IntegerType::class, [
                'label' => "Etage"
            ])
            ->add('jointness')
            ->add('washroom', IntegerType::class, [
                'label' => "salle d'eau"
            ])
            ->add('bathroom', IntegerType::class, [
                'label' => "salle de bain"
            ])
            ->add('wc', IntegerType::class, [
                'label' => "wc"
            ])
            ->add('terrace', IntegerType::class, [
                'label' => "Terasse"
            ])
            ->add('balcony', IntegerType::class, [
                'label' => "Balcon"
            ])
            ->add('sanitation', IntegerType::class, [
                'label' => "Sanitaire"
            ])
            ->add('isFurnished', CheckboxType::class, [
                'label' => 'est-il équipé ?'
            ])
            ->add('energy', ChoiceType::class, [
                'label' => 'Energie',
                'choices'  => [
                    'chauffage électrique' => "électrique",
                    'chauffage au fuel' => 'fuel',
                ],
            ])
            ->add('denomination', EntityType::class, [
                'class' => Denomination::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('houseType', EntityType::class, [
                'class' => HouseType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('apartmentType', EntityType::class, [
                'class' => ApartmentType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('landType', EntityType::class, [
                'class' => LandType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('tradeType', EntityType::class, [
                'class' => TradeType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('buildingEquipment', EntityType::class, [
                'class' => BuildingEquipment::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                        ->orderBy('b.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('houseEquipment', EntityType::class, [
                'class' => HouseEquipment::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('otherOption', EntityType::class, [
                'class' => OtherOption::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Complement::class,
        ]);
    }
}
