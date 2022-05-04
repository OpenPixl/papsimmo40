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
            ->add('ref')
            ->add('name')
            ->add('annonce')
            ->add('piece', IntegerType::class,[
                'empty_data' => 0
            ])
            ->add('room', IntegerType::class)
            ->add('isHome', CheckboxType::class, [
                    'required' => false,
                ])
            ->add('isApartment', CheckboxType::class, [
                    'required' => false,
                ])
            ->add('isLand', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isOther', CheckboxType::class, [
                'required' => false,
            ])
            ->add('otherDescription')
            ->add('surfaceLand', IntegerType::class)
            ->add('surfaceHome', IntegerType::class)
            ->add('dpeAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
                ])
            ->add('diagDpe', IntegerType::class)
            ->add('diagGpe', IntegerType::class)
            ->add('adress')
            ->add('complement')
            ->add('zipcode')
            ->add('city')
            ->add('notaryEstimate', IntegerType::class)
            ->add('applicantEstimate', IntegerType::class)
            ->add('cadasterZone')
            ->add('cadasterNum', IntegerType::class)
            ->add('cadasterSurface', IntegerType::class)
            ->add('cadasterCariez', IntegerType::class)
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
