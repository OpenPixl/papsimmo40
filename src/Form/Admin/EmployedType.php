<?php

namespace App\Form\Admin;

use App\Entity\Admin\Employed;
use App\Repository\Admin\EmployedRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EmployedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'label'=>'Adresse de connexion'
            ])
            //->add('roles')
            //->add('password')
            ->add('civility', ChoiceType::class, [
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
            ->add('firstName')
            ->add('lastName')
            //->add('slug')
            ->add('sector')
            ->add('isVerified')
            ->add('referent', EntityType::class, [
                'class' => Employed::class,
                'choice_attr' => ChoiceList::attr($this, function (?Employed $category) {
                    return $category ? ['data-data' => $category->getFirstName()] : [];
                }),
            ])
            ->add('avatarFile', FileType::class,[
                'label' => "Avatar, le fichier ne doit pas dépasser 10Mo de taille",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10000k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Attention, veuillez charger un fichier au format jpg ou png',
                    ])
                ],
            ])
            ->add('isSupprAvatar', CheckboxType::class,[
                'label' => "Supprimer",
                'required' => false
            ])
            ->add('home', TextType::class, [
                'label' => 'Domicile',
                'required' => false
            ])
            ->add('desk', TextType::class, [
                'label' => 'Bureau',
                'required' => false
            ])
            ->add('gsm', TextType::class, [
                'label' => 'Portable *',
                'required' => true
            ])
            ->add('fax')
            ->add('otherEmail', TextType::class, [
                'label' => 'Autres email',
                'required' => false
            ])
            ->add('facebook')
            ->add('instagram')
            ->add('linkedin')
            ->add('isWebpublish', CheckboxType::class, [
                'required' => false
            ])
            ->add('employedPrez', TextareaType::class,[
                'label'=>'Présentation',
                'required' => false
            ])
            ->add('dateEmployed', DateType::class, [
                'label'=> "Date d'entrée",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employed::class,
        ]);
    }
}
