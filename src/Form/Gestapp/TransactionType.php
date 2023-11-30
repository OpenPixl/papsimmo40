<?php

namespace App\Form\Gestapp;

use App\Entity\Gestapp\choice\PropertyBanner;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Transaction;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                    'Ouverture du dossier' => 'open',
                    'Promesse de vente' => 'promise',
                    'Dépôt de dossier' => 'deposit',
                    'Acte de vente définitif' => 'definitive_sale',
                    'Dossier finalisé' => 'finished'
                ],
                'choice_attr' => [
                    'Ouverture du dossier' => ['data-data' => 'open'],
                    'promesse de vente' => ['data-data' => 'promise'],
                    'Dépôt de dossier' => ['data-data' => 'deposit'],
                    'acte de vente définitif' => ['data-data' => 'definitive sale'],
                    'Dossier finalisé' => ['data-data' => 'finished']
                ],
            ])
            ->add('customer', EntityType::class, [
                'label'=> 'Client',
                'required' => true,
                'multiple' => true,
                'class' => Customer::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.lastName', 'ASC');
                },
                'choice_label' => 'firstName',
                'choice_attr' => function (Customer $customer, $key, $index) {
                    return ['data-data' => $customer->getFirstName()." ".$customer->getLastName() ];
                },
                'placeholder' => 'A définir',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
