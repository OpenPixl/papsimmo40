<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\PropertyOrientation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyOrientationFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter = new PropertyOrientation();
        $parameter->setName('A définir');
        $manager->persist($parameter);

        $parameter = new PropertyOrientation();
        $parameter->setName('Sud');
        $manager->persist($parameter);

        $parameter = new PropertyOrientation();
        $parameter->setName('Ouest');
        $manager->persist($parameter);

        $parameter = new PropertyOrientation();
        $parameter->setName('Nord');
        $manager->persist($parameter);

        $parameter = new PropertyOrientation();
        $parameter->setName('Est');
        $manager->persist($parameter);


        $manager->flush();
    }
}