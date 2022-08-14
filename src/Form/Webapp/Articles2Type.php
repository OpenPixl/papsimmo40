<?php

namespace App\Form\Webapp;

use App\Entity\Webapp\Articles;
use App\Entity\Webapp\choice\Category;
use phpDocumentor\Reflection\Types\Callable_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class Articles2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id')
            ->add('name', TextType::class, [
                'label' => "Titre de l'article"
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu'
            ])
            ->add('isShowtitle', CheckboxType::class,[
                'label' => 'Montrer le titre',
                'required' => false,
            ])
            ->add('isShowdate', CheckboxType::class,[
                'label' => 'Montrer la date',
                'required' => false,
            ])
            ->add('isShowreadmore', CheckboxType::class,[
                'label' => 'Afficher "Lire la suite ..."',
                'required' => false,
            ])
            ->add('isLink', CheckboxType::class,[
                'label' => 'Ajouter un lien',
                'required' => false,
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
            ->add('imgPosition', ChoiceType::class, [
                'label' => "Position de l'image",
                'choices'  => [
                    "Au dessus de l'article" => "Hight",
                    "A droite de l'article" => "Right",
                    "Au dessous de l'article" => "Down",
                    "A gauche de l'article" => "Left",
                ],
                'choice_attr' => [
                    "Au dessus de l'article" => ['data-data' => 'Hight'],
                    "A droite de l'article" => ['data-data' => 'Right'],
                    "Au dessous de l'article" => ['data-data' => 'Down'],
                    "A gauche de l'article" => ['data-data' => 'Left'],
                ],
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
