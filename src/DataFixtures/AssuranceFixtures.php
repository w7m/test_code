<?php

namespace App\DataFixtures;

use App\Entity\Expert;
use App\Entity\Insurer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AssuranceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('EN_en');
        for ($i = 1; $i <= 4; $i++) {
            $insurer = new Insurer();
            $insurer->setFirstName($faker->firstName);
            $insurer->setLastName($faker->lastName);
            $insurer->setPassword(password_hash('talan12345', PASSWORD_BCRYPT));
            switch ($i) {
                case 1:
                    $insurer->setEmail('admin@talan.com');
                    break;
                case 2:
                    $insurer->setEmail('financial@talan.com');
                    break;
                case 3:
                    $insurer->setEmail('receptionist@talan.com');
                    break;
                case 4:
                    $insurer->setEmail('validator@talan.com');
                    break;
                case 5:
                    $insurer->setEmail('expert@talan.com');
                    break;
            }

            $insurer->setUsername($faker->userName);
            $insurer->setPhoneNumber('71' . $faker->numberBetween($min = 100000, $max = 999999));
            $insurer->setIsActivated(true);
            switch ($i) {
                case 1:
                    $insurer->setRoles(['ROLE_ADMIN']);
                    break;
                case 2:
                    $insurer->setRoles(['ROLE_FINANCIAL']);
                    break;
                case 3:
                    $insurer->setRoles(['ROLE_RECEPTIONIST']);
                    break;
                case 4:
                    $insurer->setRoles(['ROLE_VALIDATOR']);
                    break;
                case 5:
                    $insurer->setRoles(['ROLE_EXPERT']);
                    break;
            }
            $insurer->setInsurerId($i);
            $insurer->setToken('0');
            $manager->persist($insurer);
        }
        $manager->flush();
    }

}
