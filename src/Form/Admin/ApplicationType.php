<?php

namespace App\Form\Admin;

use App\Entity\Admin\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameSite', TextType::class, [
                'label' => 'Nom du site'
            ])
            ->add('sloganSite', TextType::class, [
                'label' => 'Slogan'
            ])
            ->add('descrSite')
            ->add('isOnline')
            ->add('adminEmail')
            ->add('adminWebmaster')
            ->add('isBlockmenufluid')
            ->add('offlineMessage')
            ->add('isShowofflinemessage')
            ->add('isShowofflinelogo')
            ->add('isShowtitlesitehome')
            ->add('urlFacebook')
            ->add('urlInstagram')
            ->add('urlLinkedin')
            ->add('urlGooglebusiness')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
