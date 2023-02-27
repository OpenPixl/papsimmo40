<?php

namespace App\Form\Gestapp;


use App\Entity\Gestapp\choice\Denomination;
use App\Entity\Gestapp\choice\OtherOption;
use App\Entity\Gestapp\choice\PropertyBanner;
use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Entity\Gestapp\choice\PropertyEquipement;
use App\Entity\Gestapp\choice\PropertyOrientation;
use App\Entity\Gestapp\choice\PropertyState;
use App\Entity\Gestapp\choice\PropertyTypology;
use App\Entity\Gestapp\Complement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Partie Supérieure Form
            ->add('banner', EntityType::class, [
                'label'=> 'Bannière',
                'required' => false,
                'class' => PropertyBanner::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_attr' => function (PropertyBanner $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                },
                'placeholder' => 'A définir',
            ])
            ->add('denomination', EntityType::class, [
                'label'=> 'Catégorie de bien',
                'required' => false,
                'class' => Denomination::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_attr' => function (Denomination $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                },
                'placeholder' => 'A définir',
            ])

            // Partie "Pieces"
            ->add('washroom', IntegerType::class, [
                'label' => "salle d'eau",
                'empty_data' => 0
            ])
            ->add('bathroom', IntegerType::class, [
                'label' => "salle de bain",
                'required' => false,
                'empty_data' => 0
            ])
            ->add('wc', IntegerType::class, [
                'label' => "wc",
                'required' => false,
                'empty_data' => 0
            ])
            ->add('terrace', IntegerType::class, [
                'label' => "Terrasse",
                'required' => false,
                'empty_data' => 0
            ])
            ->add('balcony', IntegerType::class, [
                'label' => "Balcon",
                'required' => false,
                'empty_data' => 0
            ])

            // Partie "Le Bien"
            ->add('propertyState', EntityType::class, [
                'class' => PropertyState::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => 'Etat du bien',
                'choice_attr' => function (PropertyState $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                }
            ])
            ->add('propertyEnergy', EntityType::class, [
                'class' => PropertyEnergy::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => 'Energie',
                'choice_attr' => function (PropertyEnergy $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                }
            ])
            ->add('propertyTax', NumberType::class, [
                'label' => 'Taxe foncière',
                'required' => false,
                'empty_data' => 0
            ])
            ->add('propertyOrientation', EntityType::class, [
                'class' => PropertyOrientation::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => 'Orientation',
                'choice_attr' => function (PropertyOrientation $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                }
            ])
            ->add('disponibility', ChoiceType::class,[
                'label' => 'Disponibilité',
                'choices'  => [
                    'A définir' => "a-definir",
                    'Oui' => 'yes',
                    'Non' => 'no'
                ],
                'choice_attr' => [
                    'A définir' => ['data-data' => 'A définir'],
                    'Oui' => ['data-data' => 'oui'],
                    'Non' => ['data-data' => 'Non'],
                ],
            ])
            ->add('location', ChoiceType::class, [
                'label' => 'Location',
                'choices'  => [
                    'A définir' => "a-definir",
                    'Immédiatement' => 'immediatement',
                    'A partir de' => 'a-partir-de'
                ],
                'choice_attr' => [
                    'A définir' => ['data-data' => 'A définir'],
                    'Immédiatement' => ['data-data' => 'Immédiatement'],
                    'A partir de' => ['data-data' => 'A partir de'],
                ],
            ])
            ->add('disponibilityAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
                'label' => 'A partir du'
            ])

            // Partie "Equipements"
            ->add('coproperty', ChoiceType::class, [
                'label' => 'Le bien est en copropriété ?',
                'choices' => [
                    'non' => 0,
                    'oui' => 1,
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('jointness', ChoiceType::class, [
                'label' => 'Le bien est mitoyen ?',
                'choices' => [
                    'non' => 0,
                    'oui' => 1,
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('isFurnished', ChoiceType::class, [
                'label' => 'Le bien est-il meublé ?',
                'choices' => [
                    'non' => 0,
                    'oui' => 1,
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('propertyEquipment',EntityType::class, [
                'class' => PropertyEquipement::class,
                'label' => 'Equipement du bien',
                'multiple' => true,
                'choice_attr' => ChoiceList::attr($this, function (?PropertyEquipement $propertyEquipement) {
                    return $propertyEquipement ? ['data-data' => $propertyEquipement->getName()] : [];
                })
            ])
            ->add('level', IntegerType::class, [
                'label' => "Etage",
                'required' => false,
                'empty_data' => 0
            ])
            ->add('propertyTypology', EntityType::class, [
                'class' => PropertyTypology::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_attr' => function (PropertyTypology $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                },
                'label' => 'Typologie du bien'
            ])
            ->add('propertyOtheroption',EntityType::class, [
                'label'=> 'Autres options de bien',
                'class' => OtherOption::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'choice_attr' => function (OtherOption $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                }
            ])
            ->add('coproprietyTaxe', NumberType::class, [
                'label' => 'Charge de copro',
                'required' => false,
                'attr'=>[
                  'placeholder' => 'Charge de copro'
                ],
                'empty_data' => 0
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
