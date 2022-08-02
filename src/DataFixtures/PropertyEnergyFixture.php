<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\PropertyEnergy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyEnergyFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter = new PropertyEnergy();
        $parameter->setName('A définir');
        $manager->persist($parameter);

        $parameter = new PropertyEnergy();
        $parameter->setName('Au fuel');
        $manager->persist($parameter);

        $parameter = new PropertyEnergy();
        $parameter->setName('Electrique');
        $manager->persist($parameter);

        $manager->flush();
    }
}