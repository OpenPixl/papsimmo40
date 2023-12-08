<?php

namespace App\Form\Admin;

use App\Entity\Admin\Employed;
use App\Repository\Admin\EmployedRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

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
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => 'Télecharger',
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
