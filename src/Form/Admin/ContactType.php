<?php

namespace App\Form\Admin;

use App\Entity\Admin\Contact;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom et prénom *',
                'required' => true
            ])
            ->add('email', TextType::class, [
                'label' => 'Email *',
                'required' => true
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('phoneHome', TextType::class, [
                'label' => 'Fixe',
                'required' => false,
            ])
            ->add('phoneGsm', TextType::class, [
                'label' => 'Portable',
                'required' => false,
            ])
            ->add('contactBy', ChoiceType::class,[
                'choices' => [
                    'Téléphone' => 'téléphone',
                    'Email' => 'email'
                ],
                'label' => "Dans le cadre de cette démarche, vous préférez être joint par ? ",
                'required' => false,
                'data' => 'téléphone'
            ])
            ->add('isRGPD', CheckboxType::class, [
                'label' => "En soumettant ce formulaire, j'accepte que les informations saisies soient exploitées dans le cadre de la démarche de renseignements et de la relation commercial qui peut en découler."
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
