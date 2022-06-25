<?php

namespace App\Form\Webapp;

use App\Entity\Webapp\Articles;
use App\Entity\Webapp\choice\Category;
use phpDocumentor\Reflection\Types\Callable_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Titre de l'article"
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu'
            ])
            ->add('isShowtitle', ChoiceType::class,[
                'label' => 'Montrer le titre'
            ])
            ->add('isShowdate', ChoiceType::class,[
                'label' => 'Montrer la date'
            ])
            ->add('isShowreadmore', ChoiceType::class,[
                'label' => 'Afficher "Lire la suite ..."'
            ])
            ->add('isLink', ChoiceType::class,[
                'label' => 'Ajouter un lien'
            ])
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
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class
            ])
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
