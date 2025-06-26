<?php

namespace App\Form\Gestapp\Transaction;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Transaction\AddCollTransac;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class addCollaboratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('refemployed', EntityType::class, [
                'class' => Employed::class,
                'label'=> 'Catégorie',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('e')
                        ->where('e.roles = :role')
                        ->setParameter('role', '["ROLE_EMPLOYED"]')
                        ->orderBy('e.id', 'ASC');
                },
                'choice_label' => function(Employed $employed) {
                    return $employed->getFirstName() . ' ' . $employed->getLastName();
                },
                'choice_attr' => function (employed $employed, $key, $index) {
                    return ['data-data' => $employed->getFirstName().' '.$employed->getLastName() ];
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddCollTransac::class,
        ]);
    }
}