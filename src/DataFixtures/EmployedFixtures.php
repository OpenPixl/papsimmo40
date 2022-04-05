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

        $employed2 = new Employed();
        $employed2->setRoles(array('ROLE_USER'));
        $employed2->setPassword($this->passwordEncoder->hashPassword($employed2, 'Corwin_40'));
        $employed2->setEmail('xavier.burke@gmail.fr');
        $employed2->setFirstName('xavier');
        $employed2->setLastName('Burke');
        $employed2->setIsVerified(1);
        $manager->persist($employed2);

        $manager->flush();
    }
}
