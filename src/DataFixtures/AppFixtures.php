<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use App\Entity\AdminSystem;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new AdminSystem();
        $admin->setEmail('admin@admin.com')
            ->setPassword($this->encoder->encodePassword($admin,strtolower('admin')));
        $manager->persist($admin);
        $manager->flush();
    }
}
