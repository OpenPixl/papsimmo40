<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\Denomination;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DenominationFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $denomination = new Denomination();
        $denomination->setName('studio');
        $manager->persist($denomination);

        $denomination = new Denomination();
        $denomination->setName('T2');
        $manager->persist($denomination);

        $denomination = new Denomination();
        $denomination->setName('T2 Bis');
        $manager->persist($denomination);

        $denomination = new Denomination();
        $denomination->setName('T3');
        $manager->persist($denomination);

        $denomination = new Denomination();
        $denomination->setName('T3 Bis');
        $manager->persist($denomination);

        $denomination = new Denomination();
        $denomination->setName('T4');
        $manager->persist($denomination);

        $denomination = new Denomination();
        $denomination->setName('F2');
        $manager->persist($denomination);

        $manager->flush();
    }
}