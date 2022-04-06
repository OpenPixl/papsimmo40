<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\TradeType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TradeTypeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $CustomerType = new TradeType();
        $CustomerType->setName('Entrepot');
        $manager->persist($CustomerType);

        $manager->flush();
    }
}