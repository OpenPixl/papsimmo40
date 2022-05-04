<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\HouseEquipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HouseEquipementFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $house = new HouseEquipment();
        $house->setName('Cheminée');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Piscine');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Climatisation');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Four');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Lave vaisselle');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Lave linge');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('alarme');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Congélateur');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Sèche linge');
        $manager->persist($house);

        $house = new HouseEquipment();
        $house->setName('Micro-ondes');
        $manager->persist($house);

        $manager->flush();
    }
}