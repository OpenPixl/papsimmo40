<?php

namespace App\DataFixtures;

use App\Entity\Gestapp\choice\PropertyEquipement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyEquipmentFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter = new PropertyEquipement();
        $parameter->setName('A définir');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Cave');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('parking aérien');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('parking souterrain');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('accès handicapé');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('ascenceur');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('digicode');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('interphone');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('gardien');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('vidéo surveillance');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('piscine collective');
        $parameter->setCat('Building');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Cheminée');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Climatisation');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('piscine');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Four');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Lave vaisselle');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Lave linge');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('alarme');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Congélateur');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Sèche linge');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $parameter = new PropertyEquipement();
        $parameter->setName('Micro-ondes');
        $parameter->setCat('House');
        $manager->persist($parameter);

        $manager->flush();
    }
}