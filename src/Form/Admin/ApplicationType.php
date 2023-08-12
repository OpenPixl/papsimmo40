<?php

namespace App\Form\Admin;

use App\Entity\Admin\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameSite', TextType::class, [
                'label' => 'Nom du site',
                'required' => false
            ])
            ->add('sloganSite', TextType::class, [
                'label' => 'Slogan',
                'required' => false
            ])
            ->add('descrSite', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('isOnline', CheckboxType::class, [
                'label' => 'Mettre le site hors-ligne ?',
                'required' => false
            ])
            ->add('adminEmail', TextType::class, [
                'label' => "Email de l'administrateur",
                'required' => false
            ])
            ->add('adminWebmaster', TextType::class, [
                'label' => "Nom de l'administrateur",
                'required' => false
            ])
            ->add('isBlockmenufluid', CheckboxType::class, [
                'label' => 'Site en total responsive',
                'required' => false
            ])
            ->add('offlineMessage', TextareaType::class, [
                'label' => 'Message du site hors-ligne',
                'required' => false
            ])
            ->add('isShowofflinemessage', CheckboxType::class, [
                'label' => 'afficher le message du site hors-ligne ?',
                'required' => false
            ])
            ->add('isShowofflinelogo', CheckboxType::class, [
                'label' => 'afficher le logo sur le site hors-ligne ?',
                'required' => false
            ])
            ->add('isShowtitlesitehome', CheckboxType::class, [
                'label' => 'afficher le titre du site ?',
                'required' => false
            ])
            ->add('urlFacebook', TextType::class, [
                'label' => "URL Facebook",
                'required' => false
            ])
            ->add('urlInstagram', TextType::class, [
                'label' => "URL Instagram",
                'required' => false
            ])
            ->add('urlLinkedin', TextType::class, [
                'label' => "URL Linkedin",
                'required' => false
            ])
            ->add('urlGooglebusiness', TextType::class, [
                'label' => "URL Google Business",
                'required' => false
            ])
            ->add('logoFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => 'Télecharger',
            ])
            ->add('faviconFile', VichImageType::class, [
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
            'data_class' => Application::class,
        ]);
    }
}
