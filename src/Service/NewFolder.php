<?php
/**
 * Created by PhpStorm.
 * User: mabidi
 * Date: 14/01/2019
 * Time: 13:54
 */

namespace App\Service;


use App\Entity\AttachedFile;
use App\Entity\Expertise;
use App\Entity\Folder;
use App\Form\AttachedFileType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class NewFolder
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var ParameterService
     */
    private $parameterService;

    /**
     * NewFolder constructor.
     * @param EntityManagerInterface $manager
     * @param ParameterService $parameterService
     */
    public function __construct(EntityManagerInterface $manager,ParameterService $parameterService)
    {
        $this->manager = $manager;
        $this->parameterService = $parameterService;
    }

    public function newFolder($image,$img_file,$expert,$vehicle)
    {
        $folder = new Folder();
        $expertise = new Expertise();
        $image->setType('constatImage');
        $image->setName($img_file);
        $image->setImageName($img_file);
        $image->setUploadDate(new \DateTime('now'));
        $folder->addAttachedFile($image);
        $this->manager->persist($image);
        $expertise->setExpert($expert)
            ->setExpertiseLevel("1")
            ->setModificationAbility("1")
            ->setRefundStatus(Expertise::WAITING)
            ->setAssignmentDate(new \DateTime())
            ->setHonoraryParameters($this->parameterService->getParametersToHonoraryParameters());
        $folder->setRefund(0)->setState(Folder::CREATED)->setVehicle($vehicle)
            ->setRef('ref:' . $this->rendomRefFolder())->addExpertise($expertise);
        $this->manager->persist($folder);
        $this->manager->persist($expertise);
        $this->manager->flush();

    }

    private function rendomRefFolder()
    {
        $len = 10;   // total number of numbers
        $min = 1000;  // minimum
        $max = 9999;  // maximum
        $range = []; // initialize array
        foreach (range(0, $len - 1) as $i) {
            while (in_array($num = mt_rand($min, $max), $range)) ;
        }
        return $num;
    }
}