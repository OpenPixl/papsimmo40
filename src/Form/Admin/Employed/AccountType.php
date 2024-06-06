<?php

namespace App\Form\Admin\Employed;

use App\Entity\Admin\Employed;
use App\Entity\Admin\Employed\Account;
use App\Entity\Admin\Employed\AccountName;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login')
            ->add('p�assword')
            ->add('refEmployed', EntityType::class, [
                'class' => Employed::class,
                'choice_label' => 'id',
            ])
            ->add('name', EntityType::class, [
                'class' => AccountName::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
