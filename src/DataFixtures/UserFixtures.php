<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $file = fopen('public/data/user.csv', "r");
        while(! feof($file))
        {
            $data = fgetcsv($file);
            if(empty($data))
                break;
            $user = new User();
            $user->setUsername($data[1]);
            $user->setRoles(array($data[2]));
            $user->setPassword($data[3]);
            $user->setEmail($data[4]);
            $user->setJoinDate(new \DateTime($data[5]));
            $user->setCountry($data[6]);
            $user->setBirthDate(new \DateTime($data[7]));
            
            $manager->persist($user);
        }
        fclose($file);

        $manager->flush();
    }
}