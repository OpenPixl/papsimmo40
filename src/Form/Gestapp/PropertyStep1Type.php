<?php

namespace App\Form\Gestapp;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\PropertyDefinition;
use App\Entity\Gestapp\choice\PropertySscategory;
use App\Entity\Gestapp\Property;
use App\Form\SearchableEntityType;
use App\Repository\Admin\EmployedRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PropertyStep1Type extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $url){

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mandatAt', DateType::class, [
                'label'=> 'Date du DPE',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => true,
                'by_reference' => true,
            ])
            ->add('ref', TextType::class, [
                'label' => 'Référence'
            ])
            ->add('name', TextType::class, [
                'label' => 'Titre du bien',
                'required' => false,
                'empty_data' => ''
            ])
            ->add('annonce', TextareaType::class, [
                'label' => 'Annonce',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('piece', IntegerType::class,[
                'label' => 'Nombre de pièce',
                'empty_data' => 0,
                'required' => false,
            ])
            ->add('constructionAt', TextType::class, [
                'label' => 'Année de contruction',
                'attr' => [
                    'placeholder' => "au format 'aaaa'",
                ],
                'required' => false,
            ])
            ->add('room', IntegerType::class, [
                'label' => 'Nombre de chambre',
                'empty_data' => 0,
                'required' => false,
            ])
            ->add('adress',TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'empty_data' =>''
            ])
            ->add('complement',TextType::class, [
                'label' => 'Complément',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('zipcode',TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('city',TextType::class, [
                'label' => 'Commune',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('isWithoutExclusivity', CheckboxType::class, [
                'label' => 'Sans exclusivité',
                'required' => false,
            ])
            ->add("isSemiExclusivity", CheckboxType::class, [
                'label' => 'Semi-exclusivité',
                'required' => false,
            ])
            ->add('isWithExclusivity', CheckboxType::class, [
                'label' => 'Avec exclusivité',
                'required' => false,
            ])
            ->add('projet', ChoiceType::class, [
                'label' => 'Destination',
                'choices'  => [
                    'Immobilier Professionnel' => "IP",
                    'Location immobiler' => 'LH',
                    'Vente commerce,reprise' => 'RC',
                    'Commerce' => 'RC',
                    'Vente immobilier' => 'VH',
                ],
                'choice_attr' => [
                    'Immobilier Professionnel' => ['data-data' => 'Immobilier Professionnel'],
                    'Location immobiler' => ['data-data' => 'Location immobiler'],
                    'Vente commerce,reprise' => ['data-data' => 'Vente commerce,reprise'],
                    'Commerce' => ['data-data' => 'Commerce'],
                    'Vente immobilier' => ['data-data' => 'Vente immobilier'],
                ],
            ])
            ->add('propertyDefinition', EntityType::class, [
                'label'=> 'Catégorie',
                'class' => PropertyDefinition::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_attr' => function (PropertyDefinition $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                }
            ])
            ->add('sscategory', EntityType::class, [
                'label'=> 'Sous catégorie',
                'class' => PropertySscategory::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_attr' => function (PropertySscategory $product, $key, $index) {
                    return ['data-data' => $product->getName() ];
                },
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
