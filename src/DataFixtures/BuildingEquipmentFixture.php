<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\BuildingEquipment;
use App\Entity\Webapp\choice\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BuildingEquipmentFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $equipment = new BuildingEquipment();
        $equipment->setName('cave');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('parking aérien');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('parking souterrain');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('accès handicapé');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('ascenceur');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('digicode');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('interphone');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('gardien');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('vidéo surveillance');
        $manager->persist($equipment);

        $equipment = new BuildingEquipment();
        $equipment->setName('piscine');
        $manager->persist($equipment);

        $manager->flush();
    }
}