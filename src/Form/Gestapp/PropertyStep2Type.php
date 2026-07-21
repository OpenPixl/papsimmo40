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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PropertyStep2Type extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $url){

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champs commun - Bloc DPE et GES
            ->add('dpeAt', DateType::class, [
                'label'=> 'Date du DPE',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => true,
                'by_reference' => true,
            ])
            ->add('diagDpe', IntegerType::class,[
                'label'=>'résultat DPE',
                'empty_data' => 0,
                'required' => false
            ])
            ->add('diagGes', IntegerType::class, [
                'label'=>'résultat GES',
                'empty_data' => 0,
                'required' => false
            ])
            ->add('eeaYear',DateType::class, [
                'label'=> 'Année de référence',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => true,
                'by_reference' => true,
            ])
            ->add('dpeEstimateEnergyDown', IntegerType::class,[
                'label' => 'Basse',
                'empty_data' => 0,
                'required' => false
            ])
            ->add('dpeEstimateEnergyUp', IntegerType::class,[
                'label' => 'Elevée',
                'empty_data' => 0,
                'required' => false
            ])
            ->add('diagChoice', ChoiceType::class, [
                'label' => 'Diagnostique',
                'choices'  => [
                    'Obligatoire' => "obligatoire",
                    'Non soumis' => 'non soumis',
                    'Vierge' => 'vierge'
                ],
                'choice_attr' => [
                    'Obligatoire' => ['data-data' => 'obligatoire'],
                    'Non soumis' => ['data-data' => 'non soumis'],
                    'Vierge' => ['data-data' => 'vierge']
                ],
            ])
            // Champs commun - Bloc Surfaces
            ->add('surfaceLand', IntegerType::class,[
                'label'=>'Surface de terrain',
                'empty_data' => 0,
                'required' => false
            ])
            ->add('surfaceHome', IntegerType::class,[
                'label'=>'Surface habitable',
                'empty_data' => 0,
                'required' => false
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $property = $event->getData();
            $form = $event->getForm();
            //dd($property);
            if (!$property) return;

            $family = $property->getFamily();
            $rubric  = $property->getRubric();

            // Champs pour la location Commerciale
            if ($family->getId() === 4) {
                if($rubric->getId() === 8){
                    $form
                        ->add('commerceRentalAnnual',CheckboxType::class, [
                            'label' => "Le loyer et les charges présentés sont-ils annuels ?",
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceAnnualRentGlobal', NumberType::class, [
                            'label' => 'Loyer global'
                        ])
                        ->add('commerceAnnualChargeRentGlobal', NumberType::class, [
                            'label' => 'Charge global'
                        ])
                        ->add('commerceAnnualRentMeter', NumberType::class, [
                            'label' => 'Loyer au M²'
                        ])
                        ->add('commerceAnnualChargeRentMeter', NumberType::class, [
                            'label' => 'Charge au M²'
                        ])
                        ->add('commerceChargeRentMonthHt', CheckboxType::class, [
                            'label' => 'Charges mensuelles HT',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceRentAnnualCc', CheckboxType::class, [
                            'label' => 'Loyer annuel CC',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceRentAnnualHt', CheckboxType::class, [
                            'label' => 'Loyer annuel HT',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceChargeRentAnnualHt', CheckboxType::class, [
                            'label' => 'Charge annuelle HT',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceRentAnnualMeterCc', CheckboxType::class, [
                            'label' => 'Charge annuelle par M² CC ?',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceRentAnnualMeterHt', CheckboxType::class, [
                            'label' => 'Loyer annuel par M² HT ?',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceChargeRentAnnualMeterHt', CheckboxType::class, [
                            'label' => 'Charge annuelle par M² HT ?',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceSurfaceDivisible', CheckboxType::class, [
                            'label' => 'Surface Divisible ?',
                            'label_attr' => [
                                'class' => 'checkbox-inline checkbox-switch',
                            ],
                        ])
                        ->add('commerceSurfaceDivisibleMin')
                        ->add('commerceSurfaceDivisibleMax')
                        ->add('warrantyDeposit', NumberType::class, [
                            'label' => 'Dépôt de garantie'
                        ])
                    ;
                }
                elseif ($rubric->getId() === 9){
                    $form
                        ->add('price', IntegerType::class, [
                            'label' => 'Prix net vendeur',
                            'required' => false
                        ])
                        ->add('honoraires', IntegerType::class, [
                            'label' => 'honoraires',
                            'required' => false
                        ])
                        ->add('priceFai', IntegerType::class, [
                            'label' => 'Prix FAI',
                            'required' => false
                        ])
                    ;
                }
            }
            // Champs pour la location immobilière
            if ($family->getId() === 5) {
                $form
                    ->add('rent', IntegerType::class, [
                        'label' => 'Loyer',
                        'required' => false
                    ])
                    ->add('rentCharge', IntegerType::class, [
                        'label' => 'Charges mensuelles',
                        'required' => false
                    ])
                    ->add('rentChargeModsPayment', ChoiceType::class, [
                        'label' => 'Modalités de règlement',
                        'choices'  => [
                            'Forfaitaires mensuelles' => "1",
                            'Remboursement annuel par le locataire' => '2',
                            'Prévisionnelles mensuelles avec régularisation annuelle' => '3'
                        ],
                        'choice_attr' => [
                            'Forfaitaires mensuelles' => ['data-data' => 'Forfaitaires mensuelles'],
                            'Remboursement annuel par le locataire' => ['data-data' => 'Remboursement annuel par le locataire'],
                            'Prévisionnelles mensuelles avec régularisation annuelle' => ['data-data' => 'Prévisionnelles mensuelles avec régularisation annuelle']
                        ],
                    ])
                    ->add('rentChargeHonoraire', IntegerType::class, [
                        'label' => 'Honoraire sur charge locataire'
                    ])
                    ->add('rentCC', CheckboxType::class, [
                        'label' => 'Loyer CC',
                        'label_attr' => [
                            'class' => 'checkbox-inline checkbox-switch',
                        ],
                    ])
                    ->add('rentHT', CheckboxType::class, [
                        'label' => 'Loyer HT',
                        'label_attr' => [
                            'class' => 'checkbox-inline checkbox-switch',
                        ],
                    ])
                ;
            }
            // Champs pour la vente d'un bien immobilier ou professionnel
            if ($family->getId() === 8) {
                $form
                    ->add('price', IntegerType::class, [
                        'label' => 'Prix net vendeur',
                        'required' => false
                    ])
                    ->add('honoraires', IntegerType::class, [
                            'label' => 'honoraires',
                            'required' => false
                        ])
                    ->add('priceFai', IntegerType::class, [
                            'label' => 'Prix FAI',
                            'required' => false
                        ])
                    ;
            }
            // Champs pour la vente d'un bien immobilier ou professionnel
            if ($family->getId() === 6) {
                $form
                    ->add('price', IntegerType::class, [
                        'label' => 'Prix net vendeur',
                        'required' => false
                    ])
                    ->add('honoraires', IntegerType::class, [
                        'label' => 'honoraires',
                        'required' => false
                    ])
                    ->add('priceFai', IntegerType::class, [
                        'label' => 'Prix FAI',
                        'required' => false
                    ])
                ;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'csrf_protection' => false,
            // the name of the hidden HTML field that stores the token
        ]);
    }
}
