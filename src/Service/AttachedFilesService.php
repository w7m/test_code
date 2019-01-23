<?php
/**
 * Created by PhpStorm.
 * User: malejmi
 * Date: 08/01/2019
 * Time: 10:33
 */

namespace App\Service;

use App\Entity\AttachedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class AttachedFilesService
{
    private $kernel;
    private $manager;
    private $fileSystem;


    public function __construct(KernelInterface $kernel, EntityManagerInterface $manager, Filesystem $filesystem)
    {
        $this->kernel = $kernel;
        $this->manager = $manager;
        $this->fileSystem = $filesystem;
    }

    public function uploadImage($folder, $file, $type)
    {
        $image = new AttachedFile();
        $fileExension = $file->guessExtension();
        $fileName = md5(uniqid()) . '.' . $fileExension;
        $uploadDir = $this->kernel->getProjectDir() . AttachedFile::UPLOAD_DIR;
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        if ($file->move($uploadDir, $fileName)) {
            $image->setType($type);
            $image->setUploadDate(new \DateTime('now'));
            $image->setValidated(true);
            $image->setName($fileName);
            $folder->addAttachedFile($image);
            $this->manager->persist($image);
            $this->manager->persist($folder);
            $this->manager->flush();
        }
        return $image;
    }

    public function deleteFile($file)
    {
        $uploadDir = $this->kernel->getProjectDir() . AttachedFile::UPLOAD_DIR;
        if (file_exists($uploadDir . $file->getName())) {
            $this->fileSystem->remove($uploadDir . $file->getName());
        }
        $this->manager->remove($file);
        $this->manager->flush();
    }

    public function addImageBase64($folder)
    {
        $attachedFiles = $folder->getAttachedFile();
        foreach ($attachedFiles as $attachedFile) {
            $file = $this->kernel->getProjectDir() . AttachedFile::UPLOAD_DIR . $attachedFile->getname();
            if (file_exists($file)) {
                $fileBase64 = base64_encode(file_get_contents($file));
                $attachedFile->setFileBase64($fileBase64);
            }
        }
    }
}
