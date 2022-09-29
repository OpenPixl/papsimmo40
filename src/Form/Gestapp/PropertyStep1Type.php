<?php

namespace App\Form\Gestapp;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\PropertyDefinition;
use App\Entity\Gestapp\Property;
use App\Form\SearchableEntityType;
use App\Repository\Admin\EmployedRepository;
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
            ->add('refMandat', TextType::class, [
                'label' => 'Numéro de Mandat'
            ])
            ->add('ref', TextType::class, [
                'label' => 'Référence'
            ])
            ->add('name', TextType::class, [
                'label' => 'Titre du bien',
                'required' => false,
                'empty_data' =>''
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
            ->add('constructionAt', DateType::class, [
                'label' => 'Année de contruction',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
            ])
            ->add('room', IntegerType::class, [
                'label' => 'Nombre de chambre',
                'empty_data' => 0,
                'required' => false,
            ])
            ->add('propertyDefinition',EntityType::class, [
                'class' => PropertyDefinition::class,
                'label' => 'type',
                'multiple' => true,
                'choice_attr' => ChoiceList::attr($this, function (?PropertyDefinition $propertyEquipement) {
                    return $propertyEquipement ? ['data-data' => $propertyEquipement->getName()] : [];
                })
            ])
            ->add('otherDescription', TextType::class, [
                'label' => 'Autres',
                'required' => false,
                'empty_data' =>''
            ])
            ->add('adress',TextType::class, [
                'label' => 'Adresse',
                'required' => false,
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
                'label' => 'Sans exclusivité'
            ])
            ->add("isSemiExclusivity", CheckboxType::class, [
                'label' => 'Semi-exclusivité'
            ])
            ->add('isWithExclusivity', CheckboxType::class, [
                'label' => 'Avec exclusivité'
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
