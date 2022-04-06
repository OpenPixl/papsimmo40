<?php

namespace App\DataFixtures;

use App\Entity\Admin\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApplicationFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter = new Application();
        $parameter
            ->setNameSite('Paps Immo 40')
            ->setSloganSite('Votre Agence locale')
            ->setIsOnline(1)
            ->setDescrSite('Application de gestion')
            ->setAdminEmail('contact@openpixl.fr')
        ;
        $manager->persist($parameter);

        $manager->flush();
    }
}