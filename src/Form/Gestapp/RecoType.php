<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Reco;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('announceFirstName')
            ->add('announceLastName')
            ->add('announcePhone')
            ->add('announceEmail')
            ->add('customerFirstName')
            ->add('customerLastName')
            ->add('customerPhone')
            ->add('customerEmail')
            ->add('propertyAddress')
            ->add('propertyComplement')
            ->add('propertyZipcode')
            ->add('propertyCity')
            ->add('statutReco', ChoiceType::class,[
                'label' => 'Etat de la recommandation',
                'choices'  => [
                    'Ouverture du dossier' => 'reco_open',
                    'Validation par le mandataire' => 'employed_valid',
                    "Validation par l'administration" => 'admin_valid',
                    'Recommandation publiée' => 'reco_published'
                ],
                'choice_attr' => [
                    'Ouverture du dossier' => ['data-data' => 'reco_open'],
                    'Validation par le mandataire' => ['data-data' => 'employed_valid'],
                    "Validation par l'administration" => ['data-data' => 'admin_valid'],
                    'Recommandation mise en vente' => ['data-data' => 'reco_published']
                ],
            ])
            ->add('createAt')
            ->add('updateAt')
            ->add('refEmployed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reco::class,
        ]);
    }
}