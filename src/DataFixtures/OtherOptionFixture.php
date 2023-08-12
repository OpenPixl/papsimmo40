<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\OtherOption;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OtherOptionFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $other = new OtherOption();
        $other->setName('A définir');
        $manager->persist($other);

        $other = new OtherOption();
        $other->setName('Vu dégagée');
        $manager->persist($other);

        $other = new OtherOption();
        $other->setName('calme');
        $manager->persist($other);

        $other = new OtherOption();
        $other->setName('vis à vis');
        $manager->persist($other);

        $other = new OtherOption();
        $other->setName('Viager');
        $manager->persist($other);

        $manager->flush();
    }
}