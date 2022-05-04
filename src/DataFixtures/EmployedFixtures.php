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
        $employed->setPassword($this->passwordEncoder->hashPassword($employed, 'demo'));
        $employed->setEmail('contact@papsimmo.fr');
        $employed->setFirstName('demo');
        $employed->setLastName('denis');
        $employed->setIsVerified(1);
        $manager->persist($employed);

        $employed = new Employed();
        $employed->setRoles(array('ROLE_ADMIN'));
        $employed->setPassword($this->passwordEncoder->hashPassword($employed, 'ddehez40'));
        $employed->setEmail('ddzhez40@gmail.com');
        $employed->setFirstName('Denis');
        $employed->setLastName('DEHEZ');
        $employed->setIsVerified(1);
        $manager->persist($employed);

        $employed = new Employed();
        $employed->setRoles(array('ROLE_ADMIN'));
        $employed->setPassword($this->passwordEncoder->hashPassword($employed, 'utilisateur'));
        $employed->setEmail('utilisateur@papsimmo.fr');
        $employed->setFirstName('utilisateur');
        $employed->setLastName('test');
        $employed->setIsVerified(1);
        $manager->persist($employed);

        $manager->flush();
    }
}
