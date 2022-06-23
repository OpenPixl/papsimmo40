<?php

namespace App\Form\Webapp;

use App\Entity\Webapp\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('content')
            ->add('isShowtitle')
            ->add('isShowdate')
            ->add('isShowreadmore')
            ->add('isLink')
            ->add('linkText')
            ->add('state', ChoiceType::class, [
                'label' => 'Etat',
                'choices'  => [
                    'Brouillon' => "brouillon",
                    'Archivée' => 'archivee',
                    'Publiée' => 'publiée'
                ],
                'choice_attr' => [
                    'Brouillon' => ['data-data' => 'Brouillon'],
                    'Archivée' => ['data-data' => 'Archivée'],
                    'Publiée' => ['data-data' => 'Publiée'],
                ],
            ])
            ->add('category')
            ->add('articleFrontFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => 'Télecharger',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}