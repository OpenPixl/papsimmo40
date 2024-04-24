<?php

namespace App\Form\Gestapp\Transaction;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Transaction;
use App\Entity\Gestapp\Transaction\AddCollTransac;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCollTransacType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pourcentComm')
            ->add('refemployed', EntityType::class, [
                'class' => Employed::class,
'choice_label' => 'id',
            ])
            ->add('refTransac', EntityType::class, [
                'class' => Transaction::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddCollTransac::class,
        ]);
    }
}
