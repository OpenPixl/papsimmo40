<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\CustomerType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerTypeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $CustomerType = new CustomerType();
        $CustomerType->setName('client');
        $manager->persist($CustomerType);

        $manager->flush();
    }
}