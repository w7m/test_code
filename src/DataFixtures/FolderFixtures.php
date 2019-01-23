<?php
/**
 * Created by PhpStorm.
 * User: malejmi
 * Date: 08/01/2019
 * Time: 16:54
 */

namespace App\DataFixtures;


use App\Entity\Ensured;
use App\Entity\Expert;
use App\Entity\Expertise;
use App\Entity\Folder;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class FolderFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $experts = [];
        $ensureds = [];
        $faker = \Faker\Factory::create('EN_en');
        for ($j = 1; $j <= 20; $j++) {
            $expert = new Expert();
            $expert->setFirstName($faker->firstName);
            $expert->setLastName($faker->lastName);
            $expert->setPassword(password_hash('talan12345', PASSWORD_BCRYPT));
            $expert->setEmail('expert' . $j . '@talan.com');

            $expert->setUsername($faker->userName);
            $expert->setPhoneNumber('71' . $faker->numberBetween($min = 100000, $max = 999999));
            $expert->setIsActivated(true);
            $expert->setRoles(['ROLE_EXPERT']);
            $expert->setToken('0');
            $expert->setAddress($faker->address);
            $expert->setCity($faker->address);
            $expert->setCompanyName($faker->company);
            $expert->setPostalCode($faker->numberBetween($min = 1000, $max = 99999));
            $expert->setRegistrationTaxNumber($faker->numberBetween($min = 10000000, $max = 99999999));
            $manager->persist($expert);
            $experts[] = $expert;

            $ensured = new Ensured();
            $ensured->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setCIN($faker->numberBetween($min = 10000000, $max = 99999999))
                ->setPhone('71' . $faker->numberBetween($min = 100000, $max = 999999))
                ->setZipCode($faker->numberBetween($min = 1000, $max = 99999))
                ->setCity($faker->city)
                ->setEmail($faker->safeEmail);
            $manager->persist($ensured);
            $ensureds[] = $ensured;
        }
        $manager->flush();

        for ($i = 1; $i <= 20; $i++) {


            $vehicle = new Vehicle();
            $vehicle->setImageSize(10);

            $vehicle->setRegistrationNumber($faker->numberBetween($min = 1, $max = 9999) . 'tun' . $faker->numberBetween($min = 80, $max = 130));
            $vehicle->setHorsePower($faker->numberBetween($min = 3, $max = 9))
                ->setGrayCard('cartegrise.jpg');
            $vehicle->setDoorsNumber($faker->numberBetween($min = 2, $max = 5))
                ->setColor($faker->colorName)
                ->setEnsured($ensured)
                ->setDateOfRegistration($faker->dateTimeBetween('-10 years'))
                ->setType('voiture')
                ->setUpdatedAt(new \DateTime('now'))
                ->setEnsured($ensureds[mt_rand(0, 19)]);


            $folder = new Folder();
            $folder->setVehicle($vehicle);
            $folder->setRef('sinistre_' . $i);
            $state = [Folder::CREATED, Folder::WRECK_REPORT_SENT, Folder::IN_PROGRESS, Folder::TO_BE_RECONSEDERED, Folder::REASSIGNED];
            $folderState = $state[array_rand($state)];
            $folder->setState($folderState);
            if ($folderState == Folder::WRECK_REPORT_SENT) {
                $folder->setisWreck(1);
            } else {
                $folder->setisWreck(0);
            }
            $folder->setRefund(0);


            $expertise = new Expertise();
            $expertise->setFolder($folder)
                ->setExpert($experts[mt_rand(0, 19)])
                ->setAssignmentDate(new \DateTime('now'))
                ->setExpertiseLevel(1)
                ->setModificationAbility(1);

            $manager->persist($folder);
            $manager->persist($vehicle);
            $manager->persist($expertise);
        }
        $manager->flush();
    }


}