<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\PropertyTypology;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyTypologyFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter = new PropertyTypology();
        $parameter->setName('A définir');
        $manager->persist($parameter);

        $parameter = new PropertyTypology();
        $parameter->setName('Maison de plein pied');
        $manager->persist($parameter);

        $parameter = new PropertyTypology();
        $parameter->setName('Maison Castor');
        $manager->persist($parameter);

        $parameter = new PropertyTypology();
        $parameter->setName('Corps de Ferme');
        $manager->persist($parameter);

        $parameter = new PropertyTypology();
        $parameter->setName('Terrain Viabilisé');
        $manager->persist($parameter);


        $manager->flush();
    }
}