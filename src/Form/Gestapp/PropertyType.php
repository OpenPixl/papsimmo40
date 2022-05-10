<?php

namespace App\Form\Gestapp;

use App\Entity\Admin\Employed;
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

class PropertyType extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $url){

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref', TextType::class, [
                'label' => 'Référence'
            ])
            ->add('name', TextType::class, [
                'label' => 'Titre du bine'
            ])
            ->add('annonce', TextareaType::class, [
                'label' => 'Annonce'
            ])
            ->add('piece', IntegerType::class,[
                'label' => 'Nombre de pièce',
                'empty_data' => 0
            ])
            ->add('room', IntegerType::class, [
                'label' => 'Nombre de chambre',
            ])

            ->add('isHome', CheckboxType::class, [
                    'label' => 'Maison',
                    'required' => false,
                ])
            ->add('isApartment', CheckboxType::class, [
                'label' => 'Appartement',
                'required' => false,
                ])
            ->add('isLand', CheckboxType::class, [
                'label' => 'Terrain',
                'required' => false,
            ])
            ->add('isOther', CheckboxType::class, [
                'label' => 'Autres',
                'required' => false,
            ])
            ->add('otherDescription', TextType::class, [
                'label' => 'Autre...'
            ])
            ->add('surfaceLand', IntegerType::class)
            ->add('surfaceHome', IntegerType::class)
            ->add('dpeAt', DateType::class, [
                'label'=> 'Date du DPE',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
                ])
            ->add('diagDpe', IntegerType::class,[
                'label'=>'résultat DPE'
            ])
            ->add('diagGpe', IntegerType::class, [
                'label'=>'résultat GPE'
            ])
            ->add('adress',TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('complement',TextType::class, [
                'label' => 'Complément'
            ])
            ->add('zipcode',TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('city',TextType::class, [
                'label' => 'Commune'
            ])
            ->add('notaryEstimate', IntegerType::class, [
                'label' => 'Estimation du notaire'
            ])
            ->add('applicantEstimate', IntegerType::class, [
                'label' => 'Estimation du vendeur'
            ])
            ->add('cadasterZone',TextType::class, [
                'label' => 'Zone du cadastre'
            ])
            ->add('cadasterNum', IntegerType::class, [
                'label' => 'immatriculation du cadastre'
            ])
            ->add('cadasterSurface', IntegerType::class, [
                'label' => 'surface cadastrale'
            ])
            ->add('cadasterCariez', IntegerType::class, [
                'label' => 'Surface Cariez cadastrale'
            ])
            //->add('refEmployed', EntityType::class, [
            //    'class' => Employed::class,
            //    'choice_attr' => ChoiceList::attr($this, function (?Employed $category) {
            //        return $category ? ['data-data' => $category->getFirstName()] : [];
            //    }),
            //])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
