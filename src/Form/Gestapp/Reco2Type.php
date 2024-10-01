<?php

namespace App\Form\Gestapp;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\StatutReco;
use App\Entity\Gestapp\Reco;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Reco2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerCivility', ChoiceType::class, [
                'label' => 'Civilité',
                'attr' => [
                    'class' => 'radio-inline'
                ],
                'choices'  => [
                    'M.' => 1,
                    "Mme" => 2,
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('customerFirstName')
            ->add('customerLastName')
            ->add('customerMaiden', TextType::class, [
                'label' => 'Nom de jeune fille',
                'required' => false
            ])
            ->add('customerPhone')
            ->add('customerEmail')
            ->add('propertyAddress')
            ->add('propertyComplement')
            ->add('propertyZipcode')
            ->add('propertyCity')

            ->add('typeProperty', ChoiceType::class,[
                'label' => 'Type de recommandation',
                'choices'  => [
                    'Maison' => 'maison',
                    'Appartement' => 'appartement',
                    'Local commercial' => 'local_commercial',
                ],
                'choice_attr' => [
                    'Maison' => ['data-data' => 'maison'],
                    'Appartement' => ['data-data' => 'appartement'],
                    'Local commercial' => ['data-data' => 'local_commercial'],
                ],
            ])
            ->add('typeReco', ChoiceType::class,[
                'label' => 'Type de recommandation',
                'choices'  => [
                    'Vente' => 'vente',
                    'Location' => 'location',
                    'Acquisition' => 'Acquisition',
                ],
                'choice_attr' => [
                    'Vente' => ['data-data' => 'Vente'],
                    'Location' => ['data-data' => 'Location'],
                    'Acquisition' => ['data-data' => 'Acquisition']
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reco::class,
        ]);
    }
}
