<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\PropertyDefinition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyDefinitionFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter = new PropertyDefinition();
        $parameter->setName('A définir');
        $manager->persist($parameter);

        $parameter = new PropertyDefinition();
        $parameter->setName('Maison');
        $manager->persist($parameter);

        $parameter = new PropertyDefinition();
        $parameter->setName('Appartement');
        $manager->persist($parameter);

        $parameter = new PropertyDefinition();
        $parameter->setName('Terrain');
        $manager->persist($parameter);

        $parameter = new PropertyDefinition();
        $parameter->setName('Local commercial');
        $manager->persist($parameter);

        $parameter = new PropertyDefinition();
        $parameter->setName('Terrain');
        $manager->persist($parameter);

        $parameter = new PropertyDefinition();
        $parameter->setName('Immeuble');
        $manager->persist($parameter);

        $parameter = new PropertyDefinition();
        $parameter->setName('Fonds de commerce');
        $manager->persist($parameter);

        $manager->flush();
    }
}