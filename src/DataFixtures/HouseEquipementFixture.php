<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\houseEquipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HouseEquipementFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $house = new houseEquipment();
        $house->setName('Cheminée');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Piscine');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Climatisation');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Four');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Lave vaisselle');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Lave linge');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('alarme');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Congélateur');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Sèche linge');
        $manager->persist($house);

        $house = new houseEquipment();
        $house->setName('Micro-ondes');
        $manager->persist($house);

        $manager->flush();
    }
}