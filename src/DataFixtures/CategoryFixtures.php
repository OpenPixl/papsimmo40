<?php

namespace App\DataFixtures;

use App\Entity\Webapp\choice\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Sans catégorie');
        $manager->persist($category);

        $manager->flush();
    }
}