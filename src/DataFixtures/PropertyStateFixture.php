<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\PropertyState;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyStateFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter = new PropertyState();
        $parameter->setName('A définir');
        $manager->persist($parameter);

        $parameter = new PropertyState();
        $parameter->setName('Neuf');
        $manager->persist($parameter);

        $parameter = new PropertyState();
        $parameter->setName('Quelques travaux');
        $manager->persist($parameter);

        $parameter = new PropertyState();
        $parameter->setName('Vétuste');
        $manager->persist($parameter);

        $parameter = new PropertyState();
        $parameter->setName('A rénover');
        $manager->persist($parameter);


        $manager->flush();
    }
}