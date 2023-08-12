<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\CustomerChoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerChoiceFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $CustomerChoice = new CustomerChoice();
        $CustomerChoice->setName('Vendeur');
        $manager->persist($CustomerChoice);

        $CustomerChoice = new CustomerChoice();
        $CustomerChoice->setName('Client');
        $manager->persist($CustomerChoice);

        $manager->flush();
    }
}