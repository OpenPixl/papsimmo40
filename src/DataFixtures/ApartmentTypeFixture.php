<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\ApartmentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApartmentTypeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $apartment = new ApartmentType();
        $apartment->setName('duplex');
        $manager->persist($apartment);

        $manager->flush();
    }
}