<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', ChoiceType::class,[
                'label' => 'Etat du dossier',
                'choices'  => [
                    'Ouverture du dossier' => "open",
                    'promesse de vente' => 'promise',
                    'offre de prêt' => 'quotation',
                    'acte de vente définitif' => 'definitive sale',
                    'remise des clés' => 'key delivery',
                ],
                'choice_attr' => [
                    'Ouverture du dossier' => ['data-data' => 'open'],
                    'promesse de vente' => ['data-data' => 'promise'],
                    'offre de prêt' => ['data-data' => 'quotation'],
                    'acte de vente définitif' => ['data-data' => 'definitive sale'],
                    'remise des clés' => ['data-data' => 'key delivery'],
                ],
            ])
            ->add('customer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
