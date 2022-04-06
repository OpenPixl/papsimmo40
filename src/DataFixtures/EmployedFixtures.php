<?php

namespace App\DataFixtures;

use App\Entity\Admin\Employed;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmployedFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        $employed = new Employed();
        $employed->setRoles(array('ROLE_ADMIN'));
        $employed->setPassword($this->passwordEncoder->hashPassword($employed, 'admin123'));
        $employed->setEmail('contact@openpixl.fr');
        $employed->setFirstName('admin');
        $employed->setLastName('Dév');
        $employed->setIsVerified(1);
        $manager->persist($employed);

        $employed = new Employed();
        $employed->setRoles(array('ROLE_USER'));
        $employed->setPassword($this->passwordEncoder->hashPassword($employed, 'Corwin_40'));
        $employed->setEmail('xavier.burke@gmail.fr');
        $employed->setFirstName('xavier');
        $employed->setLastName('Burke');
        $employed->setIsVerified(1);
        $manager->persist($employed);

        $manager->flush();
    }
}
