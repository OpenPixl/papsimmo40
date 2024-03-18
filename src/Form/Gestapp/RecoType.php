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
                    //"Validation par l'administration" => 'admin_valid',
                    'Recommandation publiée' => 'reco_published',
                    'Publication du bien' => 'published',
                    "Dossier d'acquisition" => 'on_sale',
                    'Recommandation refusée' => 'reco_aborted',
                    'Recommandation finalisée' => 'reco_finished',
                    'Commission versée' => 'paid_commission'
                ],
                'choice_attr' => [
                    'Ouverture du dossier' => ['data-data' => 'reco_open'],
                    'Validation par le mandataire' => ['data-data' => 'employed_valid'],
                    "Validation par l'administration" => ['data-data' => 'admin_valid'],
                    'Recommandation mise en vente' => ['data-data' => 'reco_published'],
                    'Publication du bien' => ['data-data' => 'published'],
                    "Dossier d'acquisition" => ['data-data' => 'on_sale'],
                    'Recommandation refusée' => ['data-data' => 'reco_aborted'],
                    'Recommandation finalisée' => ['data-data' => 'reco_finished'],
                    'Commission versée' => ['data-data' => 'paid_commission']
                ],
            ])
            ->add('typeProperty', ChoiceType::class,[
                'label' => 'Type de recommandation',
                'choices'  => [
                    'Maison' => 'maison',
                    'Appartement' => 'appartement',
                    'Local commercial' => 'local_commercial',
                ],
                'choice_attr' => [
                    'Vente' => ['data-data' => 'Vente'],
                    'Location' => ['data-data' => 'Location'],
                ],
            ])
            ->add('typeReco', ChoiceType::class,[
                'label' => 'Type de recommandation',
                'choices'  => [
                    'Vente' => 'vente',
                    'Location' => 'location',
                ],
                'choice_attr' => [
                    'Vente' => ['data-data' => 'Vente'],
                    'Location' => ['data-data' => 'Location'],
                ],
            ])
            ->add('commission')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reco::class,
        ]);
    }
}
