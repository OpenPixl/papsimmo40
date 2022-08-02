<?php

namespace App\DataFixtures;

use App\Entity\Webapp\choice\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Sans catégorie');
        $manager->persist($category);

        $category = new Category();
        $category->setName('Actualités');
        $manager->persist($category);
        $category = new Category();
        $category->setName('Paps immo');
        $manager->persist($category);

        $manager->flush();
    }
}