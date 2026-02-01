<?php

namespace App\Form\Gestapp\Transaction;

use App\Entity\Gestapp\Transaction\Annulation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AnnulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', ChoiceType::class, [
                'label' => 'Raison de l\'annulation',
                'choices' => [
                    'Liée au délais de retraction des acquéreurs' => 'delais_retrait',
                    'Liée aux conditions suspensives - refus de l\'obtention du crédit' => 'conditions_suspensives_refus_crédit',
                    'Liée aux conditions suspensives - refus de l\'obtention du contrat' => 'conditions_suspensives_permis_de_construire',
                    'Liée aux conditions suspensives - refus de l\'acquéreur' => 'conditions_suspensives_refus_acquéreur'
                ]
            ])

            ->add('supportFile', FileType::class,[
                'label' => "Insérer le document justifiant l'annulation de la procedure de transaction. Seul, un document PDF est autorisé. Il ne doit pas dépasser 20Mo de taille.",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20000k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => '<p class="mb-0"><b>Attention,</b><br>Seul un fichier PDF est accepté dans ce type de procédure.</p>',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annulation::class,
        ]);
    }
}