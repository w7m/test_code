<?php
/**
 * Created by PhpStorm.
 * User: ebensaid
 * Date: 11/01/2019
 * Time: 13:07
 */

namespace App\DataFixtures;


use App\Entity\Parameter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ParameterFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter =new Parameter();
        $parameter->setBillPercentage(10)
            ->setExpertiseFees(30)
            ->setPhotoPrice(1)
            ->setOpeningFileExpense(15)
            ->setInsuranceCompanyName('Assurance Talan Academy')
            ->setInsurerAddress('10 rue de l\'énergie solaire, impasse n°1, Cedex 2035 Charguia 1 Tunis, Tunisie')
            ->setInsurerCity('Charguia 1')
            ->setPostcodeInsurer(2222)
            ->setInsurancePhone('+216 7001 5010');
        $manager->persist($parameter);
        $manager->flush();
    }
}