<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\HouseType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HouseTypeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $house = new HouseType();
        $house->setName('Cheminée');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Piscine');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Climatisation');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Four');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Lave vaisselle');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Lave linge');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('alarme');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Congélateur');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Sèche linge');
        $manager->persist($house);

        $house = new HouseType();
        $house->setName('Micro-ondes');
        $manager->persist($house);

        $manager->flush();
    }
}