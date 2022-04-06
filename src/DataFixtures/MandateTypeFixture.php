<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\MandateType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MandateTypeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $mandat = new MandateType();
        $mandat->setName('Mandat de vente avec exclusivité');
        $manager->persist($mandat);

        $mandat = new MandateType();
        $mandat->setName('Mandat de vente sans exclusivité');
        $manager->persist($mandat);

        $mandat = new MandateType();
        $mandat->setName('Mandat de vente semi exclusivité');
        $manager->persist($mandat);

        $manager->flush();
    }
}