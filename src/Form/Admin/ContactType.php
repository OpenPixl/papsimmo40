<?php

namespace App\Form\Admin;

use App\Entity\Admin\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('home', TextType::class, [
                'label' => 'Tél personnel'
            ])
            ->add('desk', TextType::class, [
                'label' => 'Tél bureau'
            ])
            ->add('gsm', TextType::class, [
                'label' => 'Tél portable'
            ])
            ->add('fax', TextType::class, [
                'label' => 'Fax'
            ])
            ->add('otherEmail', TextType::class, [
                'label' => 'Email'
            ])
            ->add('facebook', TextType::class, [
                'label' => 'Lien Facebook'
            ])
            ->add('instagram', TextType::class, [
                'label' => 'Line Instagram'
            ])
            ->add('linkedin', TextType::class, [
                'label' => 'Lien Linkedin'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
