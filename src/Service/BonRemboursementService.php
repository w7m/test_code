<?php
/**
 * Created by PhpStorm.
 * User: jghada
 * Date: 10/01/2019
 * Time: 08:24
 */

namespace App\Service;


use App\Entity\Paiment;
use App\Repository\ExpertiseRepository;
use App\Repository\ExpertRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;

class BonRemboursementService
{
    private $expertiseRepository;
    private $userRepository;
    private $expertRepository;
    private $manager;

    public function __construct(ExpertiseRepository $expertiseRepository, UserRepository $userRepository, ExpertRepository $expertRepository, ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->expertiseRepository = $expertiseRepository;
        $this->expertRepository = $expertRepository;

    }

    public function creerPaiement($expert)
    {
        $total = 0;
        $expertises = $this->expertiseRepository->getValidatedExpertise($expert);
        $totalHonorary = $this->expertiseRepository->getTotalHonorary($expert);
        $paiement = new Paiment();
    $paiement->setReference(substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10));
        $paiement->setAmount(floatval($totalHonorary[1]));
        $paiement->setPaimentDate(new \DateTime());
        foreach ($expertises as $expertise) {
            $expertise->setRefundStatus('refunded');
            $paiement->addExpertise($expertise);
        }
        $this->manager->persist($paiement);
        $this->manager->flush();
        return $totalHonorary;
    }


}