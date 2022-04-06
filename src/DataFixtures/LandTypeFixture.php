<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\LandType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LandTypeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $apartment = new LandType();
        $apartment->setName('Terrain viabilisé');
        $manager->persist($apartment);

        $manager->flush();
    }
}